@push('footer')
    <script src="{{ asset('assets/vendors/tinymce/tinymce.min.js?v=' . APP_VERSION) }}"></script>
    <script src="{{ asset('assets/js/editor-buttons.js?v=' . APP_VERSION) }}"></script>
    <script>
        // https://www.tiny.cloud/docs/configure/file-image-upload/
        // https://www.tiny.cloud/docs/advanced/php-upload-handler/
        tinymce.init({
            setup: function (editor) {
                editor.ui.registry.addMenuButton('socialEmbed', {
                    text: 'Social Embed',
                    fetch: function (callback) {
                        var items = [
                            {
                                type: 'menuitem',
                                text: 'Video',
                                onAction: function () {
                                    editor.windowManager.open(AddVideo)
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Audio',
                                onAction: function () {
                                    editor.windowManager.open(AddAudio)
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Facebook',
                                onAction: function () {
                                    editor.windowManager.open(AddFacebook)
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Twitter',
                                onAction: function () {
                                    editor.windowManager.open(AddTwitter)
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Instagram',
                                onAction: function () {
                                    editor.windowManager.open(AddInstagram)
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Pinterest',
                                onAction: function () {
                                    editor.windowManager.open(AddPinterest)
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'GitHub Gist',
                                onAction: function () {
                                    editor.windowManager.open(AddGist)
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'General',
                                onAction: function () {
                                    editor.windowManager.open(AddGeneral)
                                }
                            }
                        ];
                        callback(items);
                    }
                });
            },
            selector: '.text-editor',
            directionality: '{{ get_option('language_direction', 'ltr') }}',
            language: '{{ file_exists(public_path('assets/vendors/tinymce/langs/' . app()->getLocale() . '.js')) ? app()->getLocale() : 'en' }}',
            content_css: '{{ asset('assets/css/editor.css') }}',
            height: '500px',
            extended_valid_elements: 'span[style]',

            images_file_types: '{{ implode(',', get_supported_gd_image_types()) }}',
            images_upload_handler: function (blobInfo, success, failure) {
                var xhr, formData;

                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;

                xhr.open('POST', '{{ route('upload.editor') }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));

                xhr.onload = function () {
                    var json;

                    if (xhr.status !== 200) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }

                    json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location !== 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }

                    success(json.location);
                };

                formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                xhr.send(formData);
            },
            relative_urls: false,
            convert_urls: true,
            remove_script_host: false,

            plugins: 'code image wordcount link textcolor directionality table charmap lists visualblocks contextmenu',
            contextmenu: false,
            image_title: true,
            image_caption: true,
            branding: false,
            menubar: false,
            // https://www.tiny.cloud/docs/advanced/editor-control-identifiers/#toolbarcontrols
            // https://www.tiny.cloud/docs/configure/editor-appearance/#toolbarn
            toolbar1: 'formatselect | fontsizeselect | alignleft aligncenter alignright alignjustify bullist numlist | bold italic underline strikethrough | superscript subscript | ltr rtl',
            toolbar2: 'socialEmbed | link unlink image table charmap | removeformat | visualblocks code | cut copy paste undo redo',

            //fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
            block_formats: 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Preformatted=pre;Div=div;Blockquote=blockquote'
        });
    </script>

    <style>
        .no-click {
            pointer-events: none;
            opacity: 0.7;
        }
    </style>
    <script>
        $(document).on('submit', 'form.form-article', function (event) {
            $(this).find('button').addClass('no-click');

            return true;
        });
    </script>
@endpush
