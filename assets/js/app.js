/**
 * RekeningKu - Public Landing Page JavaScript
 * Clean Vanilla JS handling Clipboard API, Toast Feedback, Category Filters, and QRIS Modal
 */

document.addEventListener('DOMContentLoaded', () => {
    initCopyButtons();
    initCategoryFilters();
    initQrisModal();
});

/**
 * Show Toast Notification
 */
let toastTimeout = null;
function showToast(message) {
    const toast = document.getElementById('copyToast');
    const toastMessage = document.getElementById('toastMessage');
    if (!toast || !toastMessage) return;

    toastMessage.textContent = message;
    toast.classList.add('show');

    if (toastTimeout) {
        clearTimeout(toastTimeout);
    }

    toastTimeout = setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

/**
 * Copy text helper using modern Clipboard API with execCommand fallback
 */
async function copyToClipboard(text) {
    if (!text) return false;

    if (navigator.clipboard && window.isSecureContext) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            console.warn('Clipboard API failed, trying fallback:', err);
        }
    }

    const tempInput = document.createElement('textarea');
    tempInput.value = text;
    tempInput.style.position = 'fixed';
    tempInput.style.left = '-9999px';
    tempInput.style.top = '-9999px';
    document.body.appendChild(tempInput);
    tempInput.focus();
    tempInput.select();
    let success = false;
    try {
        success = document.execCommand('copy');
    } catch (err) {
        console.error('execCommand copy failed:', err);
    }
    document.body.removeChild(tempInput);
    return success;
}

/**
 * Initialize clipboard copy functionality
 */
function initCopyButtons() {
    const copyButtons = document.querySelectorAll('.btn-copy');

    copyButtons.forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.stopPropagation();
            const rawNumber = btn.getAttribute('data-account-number') || '';
            const cleanNumber = btn.getAttribute('data-clean-number') || rawNumber.replace(/\s+/g, '');
            const bankName = btn.getAttribute('data-bank') || 'Rekening';

            if (!cleanNumber) return;

            const success = await copyToClipboard(cleanNumber);
            if (success) {
                const originalHtml = btn.innerHTML;
                btn.classList.add('copied');
                btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Tersalin!</span>';

                setTimeout(() => {
                    btn.classList.remove('copied');
                    btn.innerHTML = originalHtml;
                }, 2000);

                showToast(`Nomor rekening ${bankName} berhasil disalin!`);
            }
        });
    });

    // Also trigger copy or open when clicking directly on the account number
    const accountNumbers = document.querySelectorAll('.card-account-number');
    accountNumbers.forEach(pan => {
        pan.addEventListener('click', () => {
            const row = pan.closest('.account-number-row');
            const openBtn = row ? row.querySelector('.btn-open-inline') : null;
            const copyBtn = row ? row.querySelector('.btn-copy') : null;
            if (openBtn) {
                openBtn.click();
            } else if (copyBtn) {
                copyBtn.click();
            }
        });
    });
}

/**
 * Category filtering (Semua, Bank, E-Wallet, QRIS)
 */
function initCategoryFilters() {
    const chips = document.querySelectorAll('.filter-chip');
    const cards = document.querySelectorAll('.account-card-wrapper');
    const counter = document.getElementById('visibleCount');

    if (!chips.length || !cards.length) return;

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');

            const filter = chip.getAttribute('data-filter');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardCategory = (card.getAttribute('data-category') || 'rekening').toLowerCase();
                const cardLabel = (card.getAttribute('data-label') || '').toLowerCase();
                const cardBank = (card.getAttribute('data-bank') || '').toLowerCase();

                let matches = false;
                if (filter === 'all') {
                    matches = true;
                } else if (filter === 'qris') {
                    matches = (cardCategory === 'qris');
                } else if (filter === 'ewallet') {
                    matches = (cardCategory === 'ewallet') || cardLabel.includes('wallet') || cardBank.includes('gopay') || cardBank.includes('ovo') || cardBank.includes('dana');
                } else if (filter === 'rekening' || filter === 'bank') {
                    matches = (cardCategory === 'rekening') && !cardLabel.includes('wallet') && !cardBank.includes('gopay') && !cardBank.includes('ovo') && !cardBank.includes('dana');
                }

                if (matches) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (counter) {
                counter.textContent = `${visibleCount} Rekening Aktif`;
            }
        });
    });
}

/**
 * QRIS Modal Popup logic (View barcode, download, copy NMID)
 */
function initQrisModal() {
    const modal = document.getElementById('qrisModal');
    if (!modal) return;

    const backdrop = document.getElementById('qrisBackdrop');
    const closeBtn = document.getElementById('qrisCloseBtn');
    const modalImg = document.getElementById('qrisModalImg');
    const modalMerchant = document.getElementById('qrisModalMerchant');
    const modalBank = document.getElementById('qrisModalBank');
    const modalNmidWrap = document.getElementById('qrisModalNmidWrap');
    const modalNmid = document.getElementById('qrisModalNmid');
    const copyNmidBtn = document.getElementById('qrisCopyNmidBtn');
    const downloadBtn = document.getElementById('qrisDownloadBtn');

    let currentNmid = '';

    function openModal(data) {
        if (!data.url) {
            showToast('Foto barcode QRIS belum tersedia untuk rekening ini.');
            return;
        }

        modalImg.src = data.url;
        modalMerchant.textContent = data.merchant || 'Merchant QRIS';
        modalBank.textContent = data.bank || 'QRIS Pembayaran';

        // Download link
        if (downloadBtn) {
            downloadBtn.href = data.url;
            const cleanMerchant = (data.merchant || 'qris').replace(/[^a-zA-Z0-9_-]/g, '_').toLowerCase();
            downloadBtn.setAttribute('download', `qris_${cleanMerchant}.png`);
        }

        // NMID block
        currentNmid = (data.nmid || '').trim();
        if (currentNmid && modalNmidWrap && modalNmid) {
            modalNmid.textContent = currentNmid;
            modalNmidWrap.style.display = 'inline-flex';
        } else if (modalNmidWrap) {
            modalNmidWrap.style.display = 'none';
        }

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => {
            if (!modal.classList.contains('active')) {
                modalImg.src = '';
            }
        }, 200);
    }

    // Attach click to all QRIS triggers
    const triggerButtons = document.querySelectorAll('.btn-view-qris');
    triggerButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            openModal({
                url: btn.getAttribute('data-qris-url'),
                merchant: btn.getAttribute('data-merchant'),
                bank: btn.getAttribute('data-bank'),
                nmid: btn.getAttribute('data-nmid')
            });
        });
    });

    // Close actions
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (backdrop) backdrop.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });

    // Copy NMID inside modal
    if (copyNmidBtn) {
        copyNmidBtn.addEventListener('click', async () => {
            if (!currentNmid) return;
            const success = await copyToClipboard(currentNmid.replace(/\s+/g, ''));
            if (success) {
                const originalHtml = copyNmidBtn.innerHTML;
                copyNmidBtn.innerHTML = '<i class="fa-solid fa-check"></i>';
                showToast('Kode NMID berhasil disalin!');
                setTimeout(() => {
                    copyNmidBtn.innerHTML = originalHtml;
                }, 2000);
            }
        });
    }
}
