// Plaats dit bestand in: ckeditor/plugins/cmstags/plugin.js

CKEDITOR.plugins.add('cmstags', {
    requires: 'widget',

    init: function(editor) {
        // Definieer de verschillende CMS widgets

        // CMS Image Widget
        editor.widgets.add('cmsimage', {
            button: 'CMS Afbeelding invoegen',
            template: '<cms-image data-imageid="0"></cms-image>',

            editables: {},

            allowedContent: 'cms-image[!data-imageid]',
            requiredContent: 'cms-image[data-imageid]',

            upcast: function(element) {
                return element.name === 'cms-image' && element.attributes['data-imageid'];
            },

            init: function() {
                var imageid = this.element.getAttribute('data-imageid');
                this.setData('imageid', imageid);
            },

            data: function() {
                this.element.setAttribute('data-imageid', this.data.imageid);
            }
        });

        // CMS Gallery Widget
        editor.widgets.add('cmsgallery', {
            button: 'CMS Galerij invoegen',
            template: '<cms-gallery data-galleryid="0"></cms-gallery>',

            editables: {},

            allowedContent: 'cms-gallery[!data-galleryid]',
            requiredContent: 'cms-gallery[data-galleryid]',

            upcast: function(element) {
                return element.name === 'cms-gallery' && element.attributes['data-galleryid'];
            },

            init: function() {
                var galleryid = this.element.getAttribute('data-galleryid');
                this.setData('galleryid', galleryid);
            },

            data: function() {
                this.element.setAttribute('data-galleryid', this.data.galleryid);
            }
        });

        // CMS YouTube Widget
        editor.widgets.add('cmsyoutube', {
            button: 'CMS YouTube invoegen',
            template: '<cms-youtube data-videoid=""></cms-youtube>',

            editables: {},

            allowedContent: 'cms-youtube[!data-videoid]',
            requiredContent: 'cms-youtube[data-videoid]',

            upcast: function(element) {
                return element.name === 'cms-youtube' && element.attributes['data-videoid'];
            },

            init: function() {
                var videoid = this.element.getAttribute('data-videoid');
                this.setData('videoid', videoid);
            },

            data: function() {
                this.element.setAttribute('data-videoid', this.data.videoid);
            }
        });

        // CMS X (Twitter) Widget
        editor.widgets.add('cmsx', {
            button: 'CMS X Post invoegen',
            template: '<cms-x data-postid=""></cms-x>',

            editables: {},

            allowedContent: 'cms-x[!data-postid]',
            requiredContent: 'cms-x[data-postid]',

            upcast: function(element) {
                return element.name === 'cms-x' && element.attributes['data-postid'];
            },

            init: function() {
                var postid = this.element.getAttribute('data-postid');
                this.setData('postid', postid);
            },

            data: function() {
                this.element.setAttribute('data-postid', this.data.postid);
            }
        });

        // CMS Instagram Widget
        editor.widgets.add('cmsinstagram', {
            button: 'CMS Instagram Post invoegen',
            template: '<cms-instagram data-postid=""></cms-instagram>',

            editables: {},

            allowedContent: 'cms-instagram[!data-postid]',
            requiredContent: 'cms-instagram[data-postid]',

            upcast: function(element) {
                return element.name === 'cms-instagram' && element.attributes['data-postid'];
            },

            init: function() {
                var postid = this.element.getAttribute('data-postid');
                this.setData('postid', postid);
            },

            data: function() {
                this.element.setAttribute('data-postid', this.data.postid);
            }
        });
    }
});