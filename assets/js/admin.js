/**
 * RekeningKu - Admin JavaScript
 * Live preview studio, modal confirmation, and sidebar toggle
 */

document.addEventListener('DOMContentLoaded', () => {
    initLiveCardPreview();
    initDeleteModal();
    initMobileNav();
    initCategoryAndQris();
});

/**
 * Live ATM Card Preview Studio
 */
function initLiveCardPreview() {
    const bankInput = document.getElementById('bank_name');
    const numberInput = document.getElementById('account_number');
    const nameInput = document.getElementById('account_name');
    const labelInput = document.getElementById('label');
    const themeSelect = document.getElementById('card_theme');

    const previewBank = document.getElementById('previewBankName');
    const previewTier = document.getElementById('previewCardTier');
    const previewNumber = document.getElementById('previewCardNumber');
    const previewHolder = document.getElementById('previewCardHolder');
    const virtualCard = document.getElementById('virtualCard');

    if (!virtualCard) return;

    const themeGradients = {
        'sapphire': 'linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%)',
        'sapphire-dark': 'linear-gradient(135deg, #1e40af 0%, #172554 100%)',
        'emerald': 'linear-gradient(135deg, #064e3b 0%, #022c22 100%)',
        'obsidian': 'linear-gradient(135deg, #1f2937 0%, #0b0f19 100%)',
        'violet': 'linear-gradient(135deg, #4c1d95 0%, #1e1b4b 100%)',
        'amber': 'linear-gradient(135deg, #7c2d12 0%, #1c1917 100%)'
    };

    if (bankInput && previewBank) {
        bankInput.addEventListener('input', (e) => {
            previewBank.textContent = e.target.value.trim() || 'NAMA BANK';
        });
    }

    if (labelInput && previewTier) {
        labelInput.addEventListener('input', (e) => {
            previewTier.textContent = (e.target.value.trim() || 'UTAMA').toUpperCase();
        });
    }

    if (numberInput && previewNumber) {
        numberInput.addEventListener('input', (e) => {
            const raw = e.target.value.replace(/\s+/g, '');
            if (!raw) {
                previewNumber.textContent = '•••• •••• •••• ••••';
            } else {
                previewNumber.textContent = raw.match(/.{1,4}/g)?.join(' ') || raw;
            }
        });
    }

    if (nameInput && previewHolder) {
        nameInput.addEventListener('input', (e) => {
            previewHolder.textContent = (e.target.value.trim() || 'NAMA PEMEGANG').toUpperCase();
        });
    }

    if (themeSelect) {
        themeSelect.addEventListener('change', (e) => {
            const selected = e.target.value;
            if (themeGradients[selected]) {
                virtualCard.style.background = themeGradients[selected];
            }
        });
    }
}

/**
 * Delete Confirmation Modal
 */
function initDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (!modal) return;

    const deleteButtons = document.querySelectorAll('.btn-trigger-delete');
    const deleteAccountNameSpan = document.getElementById('deleteAccountName');
    const deleteForm = document.getElementById('deleteForm');
    const deleteIdInput = document.getElementById('deleteIdInput');
    const cancelBtn = document.getElementById('btnCancelDelete');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');

            if (deleteIdInput) deleteIdInput.value = id;
            if (deleteAccountNameSpan) deleteAccountNameSpan.textContent = name;

            modal.classList.add('active');
        });
    });

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
}

/**
 * Mobile Navigation Toggle
 */
function initMobileNav() {
    const toggleBtn = document.getElementById('mobileNavToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            const isOpen = sidebar.classList.toggle('open');
            if (backdrop) {
                backdrop.classList.toggle('active', isOpen);
            }
        });

        if (backdrop) {
            backdrop.addEventListener('click', () => {
                sidebar.classList.remove('open');
                backdrop.classList.remove('active');
            });
        }

        // Close sidebar when clicking links inside on mobile
        sidebar.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 860) {
                    sidebar.classList.remove('open');
                    if (backdrop) backdrop.classList.remove('active');
                }
            });
        });
    }
}

/**
 * Handle Category Radio Switcher & QRIS Upload Live Preview
 */
function initCategoryAndQris() {
    const categoryRadios = document.querySelectorAll('input[name="category"]');
    const qrisSection = document.getElementById('qrisUploadSection');
    const qrisInput = document.getElementById('qris_image');
    const previewImg = document.getElementById('qrisImagePreview');
    const previewHolder = document.getElementById('qrisPreviewHolder');
    const uploadPrompt = document.getElementById('qrisUploadPrompt');
    const btnRemove = document.getElementById('btnRemoveQris');
    const bankInput = document.getElementById('bank_name');
    const labelBank = document.getElementById('labelBankName');
    const numberInput = document.getElementById('account_number');
    const previewTier = document.getElementById('previewCardTier');
    const previewBank = document.getElementById('previewBankName');
    const labelInput = document.getElementById('label');

    if (!categoryRadios.length) return;

    function applyCategory(cat) {
        if (cat === 'qris') {
            if (qrisSection) qrisSection.style.display = 'block';
            if (labelBank) labelBank.innerHTML = 'Nama Merchant / Penerbit QRIS <span class="required">*</span>';
            if (bankInput) bankInput.placeholder = 'Contoh: QRIS Toko Saya, QRIS Merchant';
            if (numberInput) numberInput.placeholder = 'NMID atau nomor merchant (opsional)';
            if (previewTier && labelInput && !labelInput.value) previewTier.textContent = 'QRIS';
            if (previewBank && bankInput && !bankInput.value) previewBank.textContent = 'QRIS ALL PAYMENT';
        } else if (cat === 'ewallet') {
            if (qrisSection) qrisSection.style.display = 'none';
            if (labelBank) labelBank.innerHTML = 'Nama E-Wallet <span class="required">*</span>';
            if (bankInput) bankInput.placeholder = 'Contoh: GoPay, OVO, DANA, ShopeePay';
            if (numberInput) numberInput.placeholder = 'Contoh: 0812 3456 7890';
            if (previewTier && labelInput && !labelInput.value) previewTier.textContent = 'E-WALLET';
            if (previewBank && bankInput && !bankInput.value) previewBank.textContent = 'E-WALLET';
        } else {
            if (qrisSection) qrisSection.style.display = 'none';
            if (labelBank) labelBank.innerHTML = 'Nama Bank / Institusi <span class="required">*</span>';
            if (bankInput) bankInput.placeholder = 'Contoh: BCA, Bank Mandiri, BRI';
            if (numberInput) numberInput.placeholder = 'Contoh: 1234 5678 9012';
            if (previewTier && labelInput && !labelInput.value) previewTier.textContent = 'UTAMA';
            if (previewBank && bankInput && !bankInput.value) previewBank.textContent = 'NAMA BANK';
        }
    }

    categoryRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.checked) applyCategory(radio.value);
        });
        if (radio.checked) applyCategory(radio.value);
    });

    // File Preview
    if (qrisInput) {
        qrisInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    if (previewImg) previewImg.src = event.target.result;
                    if (previewHolder) previewHolder.style.display = 'block';
                    if (uploadPrompt) uploadPrompt.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (btnRemove && qrisInput) {
        btnRemove.addEventListener('click', (e) => {
            e.preventDefault();
            qrisInput.value = '';
            if (previewImg) previewImg.src = '';
            if (previewHolder) previewHolder.style.display = 'none';
            if (uploadPrompt) uploadPrompt.style.display = 'flex';
        });
    }
}
