<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CKEditor Test</title>
    <script src="vendor/ckeditor/ckeditor/ckeditor.js"></script>
</head>
<body>
<h1>Beschikabre tags</h1>
<p>De volgende beschikbare tags kan je gebruiken in de 'broncode'-modus. Niet alles is getest, maar wat niet werkt is eenvoudig te implementeren.</p>
<pre>&lt;cms-image data-image-id=&quot;42&quot;&gt;&lt;/cms-image&gt;
&lt;cms-gallery data-gallery-id=&quot;5&quot;&gt;&lt;/cms-gallery&gt;
&lt;cms-youtube data-youtube-id=&quot;AbCdEfG123&quot;&gt;&lt;/cms-youtube&gt;
&lt;cms-x data-x-id=&quot;1234567890&quot;&gt;&lt;/cms-x&gt;
&lt;cms-instagram data-instagram-id=&quot;1234567890&quot;&gt;&lt;/cms-instagram&gt;</pre>

    <textarea name="editor" id="editor">
        <p>Test tekst</p>
    </textarea>

    <script>
        // Voeg dit toe aan je CKEditor configuratie

        // Stap 1: Definieer waar je custom plugin staat (buiten vendor directory)
        CKEDITOR.plugins.addExternal('cmstags', 'js/ckeditor-plugins/cmstags/', 'plugin.js');

        // Stap 2: Initialiseer CKEditor met de plugin
        CKEDITOR.replace('editor', {
            // Voeg de cmstags plugin toe (widget is al standaard beschikbaar in CKEditor 4.3+)
            //extraPlugins: 'cmstags',

            // Sta speciale tags toe
            autoParagraph: false,
            allowedContent: true,
            extraAllowedContent: 'cms-*[*]{*}(*)',
            // Laad de custom CSS in de editor content
            contentsCss: [
                'js/ckeditor-plugins/cmstags/styles/cmstags.css'
            ],

            // Overige configuratie naar wens
            height: 400,
            language: 'nl',

            // Optioneel: schakel Source knop in als die er nog niet is
            toolbar: [
                { name: 'document', items: ['Source', '-', 'Save', 'NewPage', 'Preview'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                '/',
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                { name: 'links', items: ['Link', 'Unlink'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule'] },
                '/',
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] }
            ]
        });

    </script>
</body>
</html>