<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->integer('branch_id');
        $productIds = collect($request->input('products', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();
        $copies = max(1, min(12, (int) $request->input('copies', 1)));

        $products = Product::with(['branches.branch', 'category'])->orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $selectedBranch = $branches->firstWhere('id', $branchId);

        $selectedProducts = $productIds->isNotEmpty()
            ? $products->whereIn('id', $productIds)->values()
            : collect();

        $printerSettings = Setting::whereIn('key', [
            'label_paper_size',
            'label_columns',
            'label_show_store_name',
        ])->pluck('value', 'key');

        return view('labels.index', [
            'branches' => $branches,
            'products' => $products,
            'selectedProducts' => $selectedProducts,
            'selectedBranch' => $selectedBranch,
            'selectedBranchId' => $branchId,
            'copies' => $copies,
            'labelPreview' => $selectedProducts->map(function (Product $product) use ($branchId, $selectedBranch) {
                $branchProduct = $product->branches->firstWhere('branch_id', $branchId);
                $barcodeValue = strtoupper(preg_replace('/[^0-9A-Z\\-\\.\\$\\/\\+% ]/', '', $product->barcode ?: $product->sku ?: 'ITEM'.$product->id));
                $barcodeValue = trim($barcodeValue) !== '' ? trim($barcodeValue) : 'ITEM'.$product->id;

                return [
                    'product' => $product,
                    'branch_product' => $branchProduct,
                    'store_name' => $selectedBranch?->name ?? config('app.name', 'POS Store'),
                    'barcode_value' => $barcodeValue,
                    'barcode_svg' => $this->generateCode39Svg($barcodeValue),
                ];
            }),
            'printerSettings' => [
                'paper_size' => $printerSettings['label_paper_size'] ?? 'A4 12 Label',
                'columns' => (int) ($printerSettings['label_columns'] ?? 3),
                'show_store_name' => (string) ($printerSettings['label_show_store_name'] ?? '1') === '1',
            ],
        ]);
    }

    private function generateCode39Svg(string $value): string
    {
        $patterns = [
            '0' => 'nnnwwnwnn',
            '1' => 'wnnwnnnnw',
            '2' => 'nnwwnnnnw',
            '3' => 'wnwwnnnnn',
            '4' => 'nnnwwnnnw',
            '5' => 'wnnwwnnnn',
            '6' => 'nnwwwnnnn',
            '7' => 'nnnwnnwnw',
            '8' => 'wnnwnnwnn',
            '9' => 'nnwwnnwnn',
            'A' => 'wnnnnwnnw',
            'B' => 'nnwnnwnnw',
            'C' => 'wnwnnwnnn',
            'D' => 'nnnnwwnnw',
            'E' => 'wnnnwwnnn',
            'F' => 'nnwnwwnnn',
            'G' => 'nnnnnwwnw',
            'H' => 'wnnnnwwnn',
            'I' => 'nnwnnwwnn',
            'J' => 'nnnnwwwnn',
            'K' => 'wnnnnnnww',
            'L' => 'nnwnnnnww',
            'M' => 'wnwnnnnwn',
            'N' => 'nnnnwnnww',
            'O' => 'wnnnwnnwn',
            'P' => 'nnwnwnnwn',
            'Q' => 'nnnnnnwww',
            'R' => 'wnnnnnwwn',
            'S' => 'nnwnnnwwn',
            'T' => 'nnnnwnwwn',
            'U' => 'wwnnnnnnw',
            'V' => 'nwwnnnnnw',
            'W' => 'wwwnnnnnn',
            'X' => 'nwnnwnnnw',
            'Y' => 'wwnnwnnnn',
            'Z' => 'nwwnwnnnn',
            '-' => 'nwnnnnwnw',
            '.' => 'wwnnnnwnn',
            ' ' => 'nwwnnnwnn',
            '$' => 'nwnwnwnnn',
            '/' => 'nwnwnnnwn',
            '+' => 'nwnnnwnwn',
            '%' => 'nnnwnwnwn',
            '*' => 'nwnnwnwnn',
        ];

        $encoded = '*'.strtoupper($value).'*';
        $narrow = 2;
        $wide = 5;
        $gap = 2;
        $height = 64;
        $quiet = 12;
        $x = $quiet;
        $bars = [];

        foreach (str_split($encoded) as $character) {
            if (! isset($patterns[$character])) {
                continue;
            }

            $pattern = $patterns[$character];

            foreach (str_split($pattern) as $index => $widthType) {
                $width = $widthType === 'w' ? $wide : $narrow;
                $isBar = $index % 2 === 0;

                if ($isBar) {
                    $bars[] = sprintf(
                        '<rect x="%d" y="0" width="%d" height="%d" rx="0.6" fill="#0f172a"></rect>',
                        $x,
                        $width,
                        $height
                    );
                }

                $x += $width;
            }

            $x += $gap;
        }

        $width = $x + $quiet;

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %1$d %2$d" preserveAspectRatio="none" role="img" aria-label="Barcode %3$s">%4$s</svg>',
            $width,
            $height,
            e($value),
            implode('', $bars)
        );
    }
}
