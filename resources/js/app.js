import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.dashboardLayout = () => ({
    sidebarOpen: false,
    confirmOpen: false,
    confirmMessage: '',
    pendingForm: null,

    interceptSubmit(event) {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.classList.contains('delete-form')) {
            return;
        }

        if (form.dataset.confirmed === 'true') {
            delete form.dataset.confirmed;
            return;
        }

        event.preventDefault();
        this.pendingForm = form;
        this.confirmMessage = form.dataset.confirmMessage || 'Bạn có chắc chắn muốn thực hiện hành động này?';
        this.confirmOpen = true;
    },

    closeConfirmation() {
        this.confirmOpen = false;
        this.pendingForm = null;
    },

    confirmAction() {
        const form = this.pendingForm;

        if (!form) {
            return;
        }

        this.confirmOpen = false;
        this.pendingForm = null;
        form.dataset.confirmed = 'true';
        form.requestSubmit();
    },
});

window.tourImageManager = (initialDeletedImages = []) => ({
    deletedImages: initialDeletedImages.map(Number),
    newImages: [],

    addFiles(event) {
        Array.from(event.target.files).forEach((file) => {
            this.newImages.push({
                file,
                url: URL.createObjectURL(file),
                key: `${file.name}-${file.size}-${file.lastModified}-${Date.now()}-${Math.random()}`,
            });
        });

        this.syncFileInput();
    },

    removeNewImage(index) {
        URL.revokeObjectURL(this.newImages[index].url);
        this.newImages.splice(index, 1);
        this.syncFileInput();
    },

    markExistingImageForDeletion(imageId) {
        const id = Number(imageId);

        if (!this.deletedImages.includes(id)) {
            this.deletedImages.push(id);
        }
    },

    syncFileInput() {
        const dataTransfer = new DataTransfer();

        this.newImages.forEach(({ file }) => dataTransfer.items.add(file));
        this.$refs.imageInput.files = dataTransfer.files;
    },
});

Alpine.start();

const resetNavigationState = () => {
    document.documentElement.classList.remove('is-navigating');

    document.querySelectorAll('form[data-submitting="true"]').forEach((form) => {
        delete form.dataset.submitting;
    });

    document.querySelectorAll('button[aria-busy="true"], input[aria-busy="true"]').forEach((control) => {
        control.disabled = false;
        control.removeAttribute('aria-busy');
        control.classList.remove('is-submitting');
    });
};

document.addEventListener('click', (event) => {
    if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return;
    }

    const link = event.target.closest('a[href]');

    if (!link || link.hasAttribute('download') || (link.target && link.target !== '_self')) {
        return;
    }

    const url = new URL(link.href, window.location.href);
    const currentUrl = new URL(window.location.href);

    if (url.origin !== currentUrl.origin || url.protocol === 'javascript:') {
        return;
    }

    const onlyHashChanged = url.pathname === currentUrl.pathname
        && url.search === currentUrl.search
        && url.hash;

    if (onlyHashChanged) {
        return;
    }

    document.documentElement.classList.add('is-navigating');
});

document.addEventListener('submit', (event) => {
    if (event.defaultPrevented || !(event.target instanceof HTMLFormElement)) {
        return;
    }

    const form = event.target;

    if (form.dataset.submitting === 'true') {
        event.preventDefault();
        return;
    }

    form.dataset.submitting = 'true';
    document.documentElement.classList.add('is-navigating');

    form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((control) => {
        control.disabled = true;
        control.setAttribute('aria-busy', 'true');
        control.classList.add('is-submitting');
    });
});

window.addEventListener('pageshow', resetNavigationState);
