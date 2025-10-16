<?php

class CMSTagReplacer {

    public function replaceTags($html) {
        // Vervang `<cms-image>` tags
        $html = preg_replace_callback('/<cms-image data-image-id="([^"]+)"><\/cms-image>/', function ($matches) {
            return '<img src="https://example.com/images/' . $matches[1] . '">';
        }, $html);

        // Vervang `<cms-x>` tags
        $html = preg_replace_callback('/<cms-x data-tweet-id="([^"]+)"><\/cms-x>/', function ($matches) {
            return '<blockquote class="twitter-tweet"><a href="https://twitter.com/tweet_id=' . $matches[1] . '"></blockquote>';
        }, $html);

        // Vervang `<cms-youtube>` tags
        $html = preg_replace_callback('/<cms-youtube data-youtube-id="([^"]+)"><\/cms-youtube>/', function ($matches) {
            return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $matches[1] . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
        }, $html);

        return $html;
    }

}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>CKEditor 4 Demo script</title>
        <!-- Verwijs naar CKEditor JavaScript-bestand -->
        <script src="vendor/ckeditor/ckeditor/ckeditor.js"></script>
    </head>
    <body>
        <!-- 
        allowedContent: 'cms-image[data-image-id]; cms-gallery[data-gallery-id]; cms-youtube[data-youtube-id]; cms-x[data-x-id]; cms-instagram[data-instagram-id]; b;i;u;s;a;h1;h2;h3;h4;h5;h6;sub;sup;img',
        -->


        <!-- Tekstgebied voor CKEditor -->
        <pre>&lt;cms-image data-image-id=&quot;42&quot;&gt;&lt;/cms-image&gt;
&lt;cms-gallery data-gallery-id=&quot;5&quot;&gt;&lt;/cms-gallery&gt;
&lt;cms-youtube data-youtube-id=&quot;AbCdEfG123&quot;&gt;&lt;/cms-youtube&gt;
&lt;cms-x data-x-id=&quot;1234567890&quot;&gt;&lt;/cms-x&gt;
&lt;cms-instagram data-instagram-id=&quot;1234567890&quot;&gt;&lt;/cms-instagram&gt;</pre>
        <form method="POST">
            <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>

            <script>
                CKEDITOR.plugins.add('widgetDropdown', {
                    init: function(editor) {
                        editor.ui.addRichCombo('widgetDropdown', {
                            label: 'Widgets',
                            title: 'Widgets invoegen',
                            toolbar: 'insert',
                            panel: {
                                css: [CKEDITOR.basePath + 'skins/moono-lisa/editor.css'],
                                multiSelect: false,
                                attributes: { 'aria-label': 'Widgets' }
                            },

                            init: function() {
                                this.startGroup('Widget Opties');
                                this.add('image', 'Afbeelding');
                                this.add('imagegallery', 'Afbeeldingsgalerij');
                                this.add('x', 'X-post');
                                this.add('instagram', 'Instagram-post');
                                this.add('youtube', 'YouTube');
                            },

                            onClick: function(value) {
                                switch (value) {
                                    case 'image':
                                        openWidgetDialog(editor, 'Afbeelding', 'image-id');
                                        break;
                                    case 'imagegallery':
                                        openWidgetDialog(editor, 'Afbeeldingsgalerij', 'gallery-id');
                                        break;
                                    case 'youtube':
                                        openWidgetDialog(editor, 'YouTube', 'youtube-id');
                                        break;
                                    case 'image':
                                        openWidgetDialog(editor, 'Afbeelding', 'image-id');
                                        break;
                                    // Je kunt hier meer cases toevoegen als je extra widgets toevoegt
                                    default:
                                        console.warn('Onbekende widget geselecteerd:', value);
                                        break;
                                }
                            }
                        });
                    }
                });

                // Helper functie voor de dialogen
                function openWidgetDialog(editor, label, attribute) {
                    editor.openDialog('widgetDialog', function(dialog) {
                        dialog.setTitle(label + ' Invoegen');
                        dialog.getContentElement('tab-basic', 'inputField').setLabel(label + ' ID');
                        dialog.attribute = attribute;
                    });
                }

                // Definieer het dialoogvenster voor het invoeren van een ID
                CKEDITOR.dialog.add('widgetDialog', function(editor) {
                    return {
                        title: 'Widget Invoegen',
                        minWidth: 400,
                        minHeight: 100,
                        contents: [
                            {
                                id: 'tab-basic',
                                label: 'Instellingen',
                                elements: [
                                    {
                                        type: 'text',
                                        id: 'inputField',
                                        label: '',
                                        validate: CKEDITOR.dialog.validate.notEmpty("ID mag niet leeg zijn."),
                                        setup: function(widget) {
                                            this.setValue(widget.data[widget.attribute] || '');
                                        },
                                        commit: function(widget) {
                                            widget.setData(widget.attribute, this.getValue());
                                        }
                                    }
                                ]
                            }
                        ],
                        onOk: function() {
                            const dialog = this;
                            const idValue = dialog.getValueOf('tab-basic', 'inputField');
                            const attribute = dialog.attribute;

                            // Voeg het juiste element in op basis van het geselecteerde type
                            if (attribute === 'youtube-id') {
                                editor.insertHtml(`<cms-youtube data-youtube-id="${idValue}"></cms-youtube>`);
                            } else if (attribute === 'image-id') {
                                editor.insertHtml(`<cms-image data-image-id="${idValue}"></cms-image>`);
                            }
                        }
                    };
                });

                // CKEditor initialisatie
                CKEDITOR.replace('editor1', {
                    autoParagraph: false,
                    allowedContent: true,
                    extraPlugins: 'widgetDropdown,dialog',



                });
            </script>
            <input type="submit" value="Verstuur!">
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            //echo htmlspecialchars($_POST['editor1']);

            // Voorbeeld gebruik
            //$html = 'Lorem ipsum dolor sit amet, consectetur <cms-image data-image-id="42"></cms-image> adipiscing elit. <cms-tweet data-tweet-id="1234567890"></cms-tweet> Vestibulum ante ipsum primis in faucibus. <cms-video data-video-id="bA-gcQn_Puo"></cms-video>';

            $tagReplacer = new CMSTagReplacer();
            echo $tagReplacer->replaceTags($_POST['editor1']);
        }
        ?>
    </body>
</html>


