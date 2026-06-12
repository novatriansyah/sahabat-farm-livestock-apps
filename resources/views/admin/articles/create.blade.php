<x-app-layout>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.articles.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tulis Artikel Baru</h2>
        </div>

        <form id="article-form" action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid gap-6 mb-6">
                <!-- Judul -->
                <div>
                    <label for="title" class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">Judul Artikel</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Ringkasan -->
                <div>
                    <label for="summary" class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">Ringkasan Singkat (untuk preview list)</label>
                    <textarea id="summary" name="summary" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('summary') }}</textarea>
                    @error('summary') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Thumbnail -->
                    <div>
                        <label for="thumbnail_file" class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">Foto Utama / Thumbnail</label>
                        <input type="file" id="thumbnail_file" name="thumbnail_file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                        @error('thumbnail_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Video URL (Optional) -->
                    <div>
                        <label for="video_url" class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">Link Video Youtube (Opsional)</label>
                        <input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('video_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Content Quill Editor -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-900 dark:text-white">Isi Artikel</label>
                    <input type="hidden" name="content" id="content-input">
                    <div id="editor-container" class="bg-gray-50 dark:bg-gray-700 dark:text-white rounded-b-lg border border-gray-300 dark:border-gray-600" style="min-height: 350px;">
                        {!! old('content') !!}
                    </div>
                    @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Publish Toggle -->
                <div class="flex items-center">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="is_published" class="ml-2 text-sm font-semibold text-gray-900 dark:text-white">Publish langsung ke website</label>
                </div>
            </div>

            <button type="submit" class="mt-6 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-lg text-sm w-full sm:w-auto px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700">
                Simpan Artikel
            </button>
        </form>
    </div>

    <x-slot name="styles">
        <!-- Quill Editor Styles -->
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
        <style>
            .ql-editor {
                font-family: inherit;
                font-size: 1rem;
            }
            .ql-editor blockquote {
                border-left: 4px solid #d1d5db !important;
                padding-left: 1.25rem !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                font-style: italic !important;
                color: #4b5563 !important;
                background-color: #f9fafb !important;
                padding-top: 0.5rem !important;
                padding-bottom: 0.5rem !important;
            }
            .dark .ql-editor blockquote {
                background-color: rgb(30 41 59 / 0.5) !important;
                color: rgb(203 213 225) !important;
                border-left-color: rgb(75 85 99) !important;
            }
            .ql-editor iframe.ql-video {
                width: 100%;
                aspect-ratio: 16 / 9;
                border-radius: 0.75rem;
            }
            .ql-editor img {
                border-radius: 0.75rem;
                max-width: 100%;
                height: auto;
                display: block;
                margin: 1.5rem auto;
                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            }
            .ql-toolbar {
                border-top-left-radius: 0.5rem;
                border-top-right-radius: 0.5rem;
                background-color: #f9fafb;
                border-color: #d1d5db !important;
            }
            .dark .ql-toolbar {
                background-color: #374151;
                border-color: #4b5563 !important;
            }
            #editor-container {
                border-bottom-left-radius: 0.5rem;
                border-bottom-right-radius: 0.5rem;
                border-color: #d1d5db !important;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            .dark #editor-container {
                border-color: #4b5563 !important;
            }
            #editor-container:focus-within {
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 1px #3b82f6;
            }
            .dark #editor-container:focus-within {
                border-color: #60a5fa !important;
                box-shadow: 0 0 0 1px #60a5fa;
            }
            /* Dark Mode Toolbar Styling */
            .dark .ql-toolbar .ql-stroke {
                stroke: #cbd5e1 !important;
            }
            .dark .ql-toolbar .ql-fill {
                fill: #cbd5e1 !important;
            }
            .dark .ql-toolbar .ql-picker {
                color: #cbd5e1 !important;
            }
            .dark .ql-toolbar .ql-picker-options {
                background-color: #374151 !important;
                border-color: #4b5563 !important;
            }
            /* Dark Mode Tooltip / Link popup */
            .dark .ql-tooltip {
                background-color: #1f2937 !important;
                border-color: #374151 !important;
                color: #f3f4f6 !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3) !important;
            }
            .dark .ql-tooltip input {
                background-color: #374151 !important;
                border-color: #4b5563 !important;
                color: #ffffff !important;
            }
            .dark .ql-editor.ql-blank::before {
                color: #9ca3af !important;
            }
        </style>
    </x-slot>

    @push('scripts')
        <!-- Quill Editor JS -->
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var quill = new Quill('#editor-container', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike', 'blockquote'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'align': [] }],
                            ['link', 'image', 'video'],
                            ['clean']
                        ]
                    }
                });

                // Custom Image Media Handler for Quill (Asynchronous Uploads)
                var toolbar = quill.getModule('toolbar');
                toolbar.addHandler('image', function() {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.click();

                    input.onchange = function() {
                        var file = input.files[0];
                        var formData = new FormData();
                        formData.append('image', file);
                        formData.append('_token', '{{ csrf_token() }}');

                        fetch('{{ route("admin.articles.upload-media") }}', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Upload failed');
                            return response.json();
                        })
                        .then(result => {
                            var range = quill.getSelection(true);
                            quill.insertEmbed(range.index, 'image', result.url);
                        })
                        .catch(error => {
                            console.error('Error uploading media:', error);
                            alert('Gagal mengunggah gambar.');
                        });
                    };
                });

                // Form submit handler: Copy Quill HTML content to hidden input field
                var form = document.getElementById('article-form');
                form.addEventListener('submit', function() {
                    var contentInput = document.getElementById('content-input');
                    // Get HTML content of editor
                    contentInput.value = quill.getSemanticHTML();
                });
            });
        </script>
    @endpush
</x-app-layout>
