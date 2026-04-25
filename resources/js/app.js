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

function generatePassword() {
    const alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%^&*';
    const length = 12;
    const bytes = new Uint32Array(length);
    window.crypto.getRandomValues(bytes);

    return Array.from(bytes, (value) => alphabet[value % alphabet.length]).join('');
}

function initRealtimeAuthFeedback() {
    const emailInputs = document.querySelectorAll('[data-validate-email]');
    const phoneInputs = document.querySelectorAll('[data-validate-phone]');
    const passwordInputs = document.querySelectorAll('[data-password-strength-input]');
    const confirmInputs = document.querySelectorAll('[data-password-confirm-input]');
    const capsLockInputs = document.querySelectorAll('[data-capslock-target]');
    const generatorButtons = document.querySelectorAll('[data-generate-password]');

    emailInputs.forEach((input) => {
        const feedback = document.querySelector(input.dataset.validateEmail);

        if (!feedback) {
            return;
        }

        const syncEmail = () => {
            if (!input.value) {
                feedback.textContent = 'Gunakan format email aktif yang bisa dipakai untuk login.';
                feedback.className = 'mt-2 text-xs text-slate-500';
                return;
            }

            const valid = input.checkValidity();
            feedback.textContent = valid ? 'Format email terlihat valid.' : 'Format email belum valid.';
            feedback.className = `mt-2 text-xs ${valid ? 'text-emerald-600' : 'text-rose-600'}`;
        };

        input.addEventListener('input', syncEmail);
        syncEmail();
    });

    phoneInputs.forEach((input) => {
        const feedback = document.querySelector(input.dataset.validatePhone);

        if (!feedback) {
            return;
        }

        const syncPhone = () => {
            if (!input.value) {
                feedback.textContent = 'Gunakan nomor aktif 8-20 digit, boleh memakai +, spasi, atau tanda minus.';
                feedback.className = 'mt-2 text-xs text-slate-500';
                return;
            }

            const valid = /^[0-9+\-\s()]{8,20}$/.test(input.value);
            feedback.textContent = valid ? 'Format nomor telepon terlihat valid.' : 'Format nomor telepon belum valid.';
            feedback.className = `mt-2 text-xs ${valid ? 'text-emerald-600' : 'text-rose-600'}`;
        };

        input.addEventListener('input', syncPhone);
        syncPhone();
    });

    passwordInputs.forEach((input) => {
        const feedback = document.querySelector(input.dataset.passwordStrengthTarget);
        const bar = document.querySelector(input.dataset.passwordStrengthBar);

        if (!feedback || !bar) {
            return;
        }

        const syncStrength = () => {
            const value = input.value || '';
            let score = 0;

            if (value.length >= 6) score += 1;
            if (value.length >= 10) score += 1;
            if (/[A-Z]/.test(value)) score += 1;
            if (/[0-9]/.test(value)) score += 1;
            if (/[^A-Za-z0-9]/.test(value)) score += 1;

            let label = 'Masukkan minimal 6 karakter.';
            let barClass = 'bg-slate-200';
            let width = '0%';

            if (value.length > 0) {
                if (score <= 2) {
                    label = 'Kekuatan password: lemah';
                    barClass = 'bg-rose-500';
                    width = '33%';
                } else if (score <= 4) {
                    label = 'Kekuatan password: sedang';
                    barClass = 'bg-amber-500';
                    width = '66%';
                } else {
                    label = 'Kekuatan password: kuat';
                    barClass = 'bg-emerald-500';
                    width = '100%';
                }
            }

            feedback.textContent = label;
            feedback.className = `mt-2 text-xs ${score >= 5 ? 'text-emerald-600' : score >= 3 ? 'text-amber-600' : 'text-slate-500'}`;
            bar.className = `h-2 rounded-full transition-all duration-300 ${barClass}`;
            bar.style.width = width;
        };

        input.addEventListener('input', syncStrength);
        syncStrength();
    });

    confirmInputs.forEach((input) => {
        const passwordInput = document.querySelector(input.dataset.passwordConfirmInput);
        const feedback = document.querySelector(input.dataset.passwordConfirmTarget);

        if (!passwordInput || !feedback) {
            return;
        }

        const syncConfirmation = () => {
            if (!input.value) {
                feedback.textContent = 'Ulangi password yang sama.';
                feedback.className = 'mt-2 text-xs text-slate-500';
                return;
            }

            const matches = input.value === passwordInput.value;
            feedback.textContent = matches ? 'Konfirmasi password sudah cocok.' : 'Konfirmasi password belum cocok.';
            feedback.className = `mt-2 text-xs ${matches ? 'text-emerald-600' : 'text-rose-600'}`;
        };

        input.addEventListener('input', syncConfirmation);
        passwordInput.addEventListener('input', syncConfirmation);
        syncConfirmation();
    });

    capsLockInputs.forEach((input) => {
        const feedback = document.querySelector(input.dataset.capslockTarget);

        if (!feedback) {
            return;
        }

        const setCapsState = (active) => {
            feedback.textContent = active ? 'Caps Lock aktif.' : '';
            feedback.className = `mt-2 text-xs ${active ? 'text-amber-600' : 'text-slate-500'}`;
        };

        input.addEventListener('keydown', (event) => {
            setCapsState(event.getModifierState && event.getModifierState('CapsLock'));
        });

        input.addEventListener('keyup', (event) => {
            setCapsState(event.getModifierState && event.getModifierState('CapsLock'));
        });

        input.addEventListener('blur', () => setCapsState(false));
    });

    generatorButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const passwordInput = document.querySelector(button.dataset.generatePassword);
            const confirmationInput = document.querySelector(button.dataset.generatePasswordConfirm || '');

            if (!passwordInput) {
                return;
            }

            const password = generatePassword();
            passwordInput.value = password;

            if (confirmationInput) {
                confirmationInput.value = password;
                confirmationInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });
}

function initImagePreviews() {
    document.querySelectorAll('[data-image-preview-input]').forEach((input) => {
        input.addEventListener('change', () => {
            const file = input.files?.[0];
            const target = document.querySelector(input.dataset.imagePreviewTarget || '');
            const wideTarget = document.querySelector(input.dataset.imagePreviewWide || '');

            if (!file || !target || !wideTarget) {
                return;
            }

            const objectUrl = URL.createObjectURL(file);

            [target, wideTarget].forEach((element) => {
                if (element.tagName !== 'IMG') {
                    const image = document.createElement('img');
                    image.id = element.id;
                    image.alt = 'Preview asset';
                    image.className = element === target
                        ? 'h-full w-full object-contain p-3'
                        : 'h-full w-full object-contain p-2';
                    element.replaceWith(image);
                }
            });

            const refreshedTarget = document.querySelector(input.dataset.imagePreviewTarget || '');
            const refreshedWideTarget = document.querySelector(input.dataset.imagePreviewWide || '');

            if (refreshedTarget) {
                refreshedTarget.src = objectUrl;
            }

            if (refreshedWideTarget) {
                refreshedWideTarget.src = objectUrl;
            }
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
    initRealtimeAuthFeedback();
    initImagePreviews();
    initDashboardChart();
});
