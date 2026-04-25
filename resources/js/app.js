import './bootstrap';

function parseJsonDataset(value, fallback) {
    if (!value) {
        return fallback;
    }

    try {
        return JSON.parse(value);
    } catch (error) {
        console.warn('Failed to parse dashboard chart dataset.', error);
        return fallback;
    }
}

function formatCurrency(value) {
    return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
}

function initPageModals() {
    const modalElements = Array.from(document.querySelectorAll('[data-modal]'));

    if (!modalElements.length) {
        return;
    }

    const setBodyLock = () => {
        const hasOpenModal = modalElements.some((modal) => modal.classList.contains('is-open'));
        document.body.classList.toggle('overflow-hidden', hasOpenModal);
    };

    const closeModal = (modal) => {
        modal.classList.remove('is-open');
        setBodyLock();
    };

    const openModal = (modal) => {
        modal.classList.add('is-open');
        setBodyLock();
    };

    modalElements.forEach((modal) => {
        if (modal.dataset.modalAutoOpen === 'true') {
            openModal(modal);
        }

        modal.addEventListener('click', (event) => {
            if (event.target === modal || event.target.closest('[data-modal-close]')) {
                closeModal(modal);
            }
        });
    });

    document.querySelectorAll('[data-modal-open]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const modal = document.querySelector(trigger.dataset.modalOpen);

            if (modal) {
                openModal(modal);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        modalElements.forEach((modal) => {
            if (modal.classList.contains('is-open')) {
                closeModal(modal);
            }
        });
    });
}

function initPasswordToggles() {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.querySelector(button.dataset.passwordToggle);

            if (!input) {
                return;
            }

            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            button.textContent = showing ? 'Lihat' : 'Sembunyikan';
            button.setAttribute('aria-pressed', showing ? 'false' : 'true');
        });
    });
}

async function initDashboardChart() {
    const chartElement = document.getElementById('sales-trend-chart');

    if (!chartElement) {
        return;
    }

    const echarts = await import('echarts');

    const labels = parseJsonDataset(chartElement.dataset.chartLabels, []);
    const sales = parseJsonDataset(chartElement.dataset.chartSales, []).map((value) => Number(value || 0));
    const transactions = parseJsonDataset(chartElement.dataset.chartTransactions, []).map((value) => Number(value || 0));
    const palette = parseJsonDataset(chartElement.dataset.chartPalette, {
        primary: '#0ea5e9',
        fillStart: 'rgba(56, 189, 248, 0.30)',
        fillMid: 'rgba(125, 211, 252, 0.16)',
        fillEnd: 'rgba(56, 189, 248, 0.02)',
    });

    if (!labels.length) {
        return;
    }

    const modeButtons = Array.from(document.querySelectorAll('[data-chart-mode]'));
    const totalSalesLabel = document.querySelector('[data-chart-total-sales]');
    const totalTransactionsLabel = document.querySelector('[data-chart-total-transactions]');
    const totalCaptionLabel = document.querySelector('[data-chart-total-caption]');
    const peakTitleLabel = document.querySelector('[data-chart-peak-title]');
    const peakValueLabel = document.querySelector('[data-chart-peak-value]');
    const chart = echarts.init(chartElement, null, { renderer: 'canvas' });
    let activeMode = 'sales';

    const peakForMode = (mode) => {
        const source = mode === 'transactions' ? transactions : sales;
        let maxIndex = 0;

        source.forEach((value, index) => {
            if (Number(value) > Number(source[maxIndex] ?? 0)) {
                maxIndex = index;
            }
        });

        return {
            label: labels[maxIndex] ?? '-',
            value: Number(source[maxIndex] ?? 0),
        };
    };

    const syncSummary = (mode) => {
        const peak = peakForMode(mode);
        const totalSales = sales.reduce((sum, value) => sum + Number(value || 0), 0);
        const totalTransactions = transactions.reduce((sum, value) => sum + Number(value || 0), 0);

        if (totalSalesLabel && totalTransactionsLabel && totalCaptionLabel) {
            totalSalesLabel.classList.toggle('hidden', mode !== 'sales');
            totalTransactionsLabel.classList.toggle('hidden', mode !== 'transactions');
            totalCaptionLabel.textContent = mode === 'sales'
                ? `${totalTransactions.toLocaleString('id-ID')} transaksi`
                : formatCurrency(totalSales);
        }

        if (peakTitleLabel && peakValueLabel) {
            peakTitleLabel.textContent = mode === 'sales' ? 'Puncak Penjualan' : 'Puncak Transaksi';
            peakValueLabel.textContent = mode === 'sales'
                ? formatCurrency(peak.value)
                : `${peak.value.toLocaleString('id-ID')} transaksi`;
        }

        modeButtons.forEach((button) => {
            const isActive = button.dataset.chartMode === mode;
            button.classList.toggle('is-active', isActive);
            button.classList.toggle('text-slate-700', isActive);
            button.classList.toggle('text-slate-500', !isActive);
        });
    };

    const buildOption = (mode) => {
        const isTransactions = mode === 'transactions';
        const seriesData = isTransactions ? transactions : sales;

        return {
            animationDuration: 900,
            animationEasing: 'quarticOut',
            grid: {
                left: 18,
                right: 18,
                top: 28,
                bottom: 24,
                containLabel: true,
            },
            tooltip: {
                trigger: 'axis',
                backgroundColor: 'rgba(255,255,255,0.96)',
                borderColor: '#dbeafe',
                borderWidth: 1,
                textStyle: { color: '#0f172a' },
                extraCssText: 'box-shadow:0 18px 38px rgba(148,163,184,0.18); border-radius:18px; padding:12px 14px;',
                formatter(params) {
                    const point = params[0];
                    const index = point?.dataIndex ?? 0;

                    return `
                        <div style="font-weight:600; margin-bottom:4px;">${labels[index] ?? '-'}</div>
                        <div>Penjualan: ${formatCurrency(sales[index])}</div>
                        <div>Transaksi: ${Number(transactions[index] ?? 0).toLocaleString('id-ID')}</div>
                    `;
                },
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: labels,
                axisLine: { lineStyle: { color: '#dbeafe' } },
                axisTick: { show: false },
                axisLabel: {
                    color: '#94a3b8',
                    fontSize: 11,
                    margin: 14,
                },
            },
            yAxis: {
                type: 'value',
                splitNumber: 4,
                axisLine: { show: false },
                axisTick: { show: false },
                axisLabel: {
                    color: '#94a3b8',
                    fontSize: 11,
                    formatter(value) {
                        return isTransactions
                            ? Number(value).toLocaleString('id-ID')
                            : formatCurrency(value);
                    },
                },
                splitLine: {
                    lineStyle: {
                        color: '#e0f2fe',
                        type: 'dashed',
                    },
                },
            },
            series: [{
                name: isTransactions ? 'Transaksi' : 'Penjualan',
                type: 'line',
                smooth: 0.42,
                data: seriesData,
                symbol: 'circle',
                symbolSize: 9,
                showSymbol: true,
                lineStyle: {
                    width: 4,
                    color: palette.primary,
                },
                itemStyle: {
                    color: palette.primary,
                    borderColor: '#f8fdff',
                    borderWidth: 3,
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: palette.fillStart },
                        { offset: 0.55, color: palette.fillMid },
                        { offset: 1, color: palette.fillEnd },
                    ]),
                },
                emphasis: {
                    focus: 'series',
                },
            }],
        };
    };

    const render = (mode) => {
        activeMode = mode;
        chart.setOption(buildOption(mode), true);
        syncSummary(mode);
    };

    modeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            render(button.dataset.chartMode || 'sales');
        });
    });

    render(activeMode);

    const resizeChart = () => chart.resize();
    window.addEventListener('resize', resizeChart);

    if ('ResizeObserver' in window) {
        const observer = new ResizeObserver(() => resizeChart());
        observer.observe(chartElement);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initPageModals();
    initPasswordToggles();
    initDashboardChart();
});
