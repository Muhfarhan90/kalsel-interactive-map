const richTextEditors = document.querySelectorAll('[data-rich-text-editor]');

if (richTextEditors.length) {
    Promise.all([import('quill'), import('quill/dist/quill.snow.css')])
        .then(([{ default: Quill }]) => {
            richTextEditors.forEach((container) => {
                const textarea = container.querySelector('textarea');
                const surface = container.querySelector('[data-rich-text-editor-surface]');
                const label = container.querySelector('label');
                const editor = new Quill(surface, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ header: [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                        ],
                    },
                });

                if (textarea.value) {
                    editor.setContents(editor.clipboard.convert({ html: textarea.value }));
                }

                textarea.required = false;
                textarea.classList.add('hidden');
                surface.classList.remove('hidden');
                editor.root.setAttribute('aria-label', label.textContent.trim());
                editor.root.setAttribute('aria-required', 'true');
                label.addEventListener('click', () => editor.focus());

                const syncValue = () => {
                    textarea.value = editor.getText().trim() ? editor.getSemanticHTML() : '';
                };

                editor.on('text-change', syncValue);
                syncValue();
            });
        })
        .catch((error) => console.error('Editor deskripsi gagal dimuat:', error));
}
