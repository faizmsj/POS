<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->keyBy('key');

        $defaultSettings = $this->settingDefinitions()->map(function (array $setting) use ($settings) {
            $existing = $settings->get($setting['key']);

            return [
                'key' => $setting['key'],
                'label' => $setting['label'],
                'group' => $setting['group'],
                'value' => $existing?->value ?? $setting['value'],
                'id' => $existing?->id,
            ];
        })->groupBy('group');

        return view('settings.index', [
            'settings' => $settings->values(),
            'settingGroups' => $defaultSettings,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
            'branding_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:3072',
            'branding_favicon' => 'nullable|file|mimes:png,ico,jpg,jpeg,webp,svg|max:2048',
        ]);

        $settingDefinitions = $this->settingDefinitions()->keyBy('key');

        foreach ($request->input('settings', []) as $key => $value) {
            $definition = $settingDefinitions->get($key);

            if (! $definition) {
                continue;
            }

            $normalizedValue = $value;

            if (is_string($value)) {
                $decoded = json_decode($value, true);
                $normalizedValue = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
            }

            Setting::updateOrCreate([
                'key' => $key,
            ], [
                'value' => $normalizedValue,
                'group' => $definition['group'],
            ]);
        }

        if ($request->hasFile('branding_logo')) {
            Setting::updateOrCreate([
                'key' => 'store_logo',
            ], [
                'value' => $this->storeBrandingAsset($request, 'branding_logo', 'store_logo', 'branding-logo'),
                'group' => 'Umum',
            ]);
        }

        if ($request->hasFile('branding_favicon')) {
            Setting::updateOrCreate([
                'key' => 'store_favicon',
            ], [
                'value' => $this->storeBrandingAsset($request, 'branding_favicon', 'store_favicon', 'branding-favicon'),
                'group' => 'Umum',
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Semua pengaturan berhasil disimpan.');
    }

    public function receiptPreview()
    {
        $sale = Sale::with(['branch', 'customer', 'items.product'])->latest()->first();

        abort_if(! $sale, 404, 'Belum ada transaksi untuk preview nota.');

        $settings = Setting::whereIn('key', [
            'store_name',
            'receipt_footer',
            'printer_paper_width',
            'printer_show_logo',
            'store_logo',
            'store_favicon',
        ])->pluck('value', 'key');

        return view('settings.receipt-preview', [
            'sale' => $sale,
            'receiptSettings' => [
                'store_name' => $settings['store_name'] ?? 'Kasir Pusat Store',
                'receipt_footer' => $settings['receipt_footer'] ?? 'Terima kasih telah berbelanja',
                'paper_width' => $settings['printer_paper_width'] ?? '80mm',
                'show_logo' => (string) ($settings['printer_show_logo'] ?? '0') === '1',
                'store_logo' => Setting::assetUrl($settings['store_logo'] ?? null),
                'store_favicon' => Setting::assetUrl($settings['store_favicon'] ?? null),
            ],
        ]);
    }

    private function settingDefinitions()
    {
        return collect([
            ['key' => 'store_name', 'label' => 'Nama Toko', 'value' => 'Kasir Pusat Store', 'group' => 'Umum'],
            ['key' => 'store_logo', 'label' => 'Logo Toko', 'value' => '', 'group' => 'Umum'],
            ['key' => 'store_favicon', 'label' => 'Favicon Toko', 'value' => '', 'group' => 'Umum'],
            ['key' => 'invoice_prefix', 'label' => 'Prefix Invoice', 'value' => 'PST', 'group' => 'POS'],
            ['key' => 'default_tax_percent', 'label' => 'PPN Default (%)', 'value' => '11', 'group' => 'POS'],
            ['key' => 'loyalty_enabled', 'label' => 'Aktifkan Loyalty', 'value' => '1', 'group' => 'Loyalty'],
            ['key' => 'points_per_amount', 'label' => 'Rupiah per 1 Poin', 'value' => '1000', 'group' => 'Loyalty'],
            ['key' => 'points_redeem_minimum', 'label' => 'Minimum Penukaran Poin', 'value' => '100', 'group' => 'Loyalty'],
            ['key' => 'points_redeem_value', 'label' => 'Nilai Diskon per Paket', 'value' => '1000', 'group' => 'Loyalty'],
            ['key' => 'ppob_markup_percent', 'label' => 'Markup PPOB (%)', 'value' => '5', 'group' => 'PPOB'],
            ['key' => 'printer_name', 'label' => 'Nama Printer', 'value' => 'POS-Printer-01', 'group' => 'Printer'],
            ['key' => 'printer_paper_width', 'label' => 'Lebar Kertas Nota', 'value' => '80mm', 'group' => 'Printer'],
            ['key' => 'printer_copies', 'label' => 'Jumlah Cetak Default', 'value' => '1', 'group' => 'Printer'],
            ['key' => 'printer_show_logo', 'label' => 'Tampilkan Logo Nota', 'value' => '0', 'group' => 'Printer'],
            ['key' => 'receipt_footer', 'label' => 'Footer Struk', 'value' => 'Terima kasih telah berbelanja', 'group' => 'Printer'],
            ['key' => 'label_paper_size', 'label' => 'Ukuran Kertas Label', 'value' => 'A4 12 Label', 'group' => 'Printer'],
            ['key' => 'label_columns', 'label' => 'Kolom Label per Halaman', 'value' => '3', 'group' => 'Printer'],
            ['key' => 'label_show_store_name', 'label' => 'Tampilkan Nama Toko di Label', 'value' => '1', 'group' => 'Printer'],
        ]);
    }

    private function storeBrandingAsset(Request $request, string $field, string $settingKey, string $prefix): string
    {
        $directory = public_path('uploads/branding');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $existingLogo = Setting::valueOf($settingKey);
        if (is_string($existingLogo) && Str::startsWith($existingLogo, '/uploads/branding/')) {
            $existingPath = public_path(ltrim($existingLogo, '/'));

            if (File::exists($existingPath)) {
                File::delete($existingPath);
            }
        }

        $file = $request->file($field);
        $filename = $prefix.'-'.now()->format('YmdHis').'-'.Str::random(8).'.'.$file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return '/uploads/branding/'.$filename;
    }
}
