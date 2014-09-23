(function(factory) {
    if (typeof define === "function" && define.amd) {
        define(["jquery"], factory);
    } else {
        factory(jQuery);
    }
})(function($) {

    "use strict";

    var log = function(o) {
        try {
            console.log(o)
        } catch (e) {
        }
    };

    function CropEventPicture($element) {
        this.$container = $element;

        this.$eventPictureLink = this.$container.find(".eventPicture-link");
        this.$eventPicture = this.$eventPictureLink.find("img");
        this.$eventPictureModal = this.$container.find(".eventPicture-modal");
        this.$loading = this.$container.find(".loading");

        this.$eventPictureForm = this.$eventPictureModal.find(".eventPicture-form");
        this.$eventPictureUpload = this.$eventPictureForm.find(".eventPicture-upload");
        this.$eventPictureSrc = this.$eventPictureForm.find(".eventPicture-src");
        this.$eventPictureData = this.$eventPictureForm.find(".eventPicture-data");
        this.$eventPictureInput = this.$eventPictureForm.find(".eventPicture-input");
        this.$eventPictureSave = this.$eventPictureForm.find(".eventPicture-save");

        this.$eventPictureWrapper = this.$eventPictureModal.find(".eventPicture-wrapper");
        this.$eventPicturePreview = this.$eventPictureModal.find(".eventPicture-preview");

        this.init();
    }

    CropEventPicture.prototype = {
        constructor: CropEventPicture,
        support: {
            fileList: !!$("<input type=\"file\">").prop("files"),
            fileReader: !!window.FileReader,
            formData: !!window.FormData
        },
        init: function() {
            this.support.datauri = this.support.fileList && this.support.fileReader;

            if (!this.support.formData) {
                this.initIframe();
            }

            this.initTooltip();
            this.initModal();
            this.addListener();
        },
        addListener: function() {
            this.$eventPictureLink.on("click", $.proxy(this.click, this));
            this.$eventPictureInput.on("change", $.proxy(this.change, this));
            this.$eventPictureForm.on("submit", $.proxy(this.submit, this));
        },
        initTooltip: function() {
            this.$eventPictureLink.tooltip({
                placement: "bottom"
            });
        },
        initModal: function() {
            this.$eventPictureModal.modal("hide");
            this.initPreview();
        },
        initPreview: function() {
            var url = this.$eventPicture.attr("src");
            this.$eventPictureWrapper.empty().html('<img src="' + url + '">');
        },
        initIframe: function() {
            var iframeName = "eventPicture-iframe-" + Math.random().toString().replace(".", ""),
                    $iframe = $('<iframe name="' + iframeName + '" style="display:none;"></iframe>'),
                    firstLoad = true,
                    _this = this;

            this.$iframe = $iframe;
            this.$eventPictureForm.attr("target", iframeName).after($iframe);

            this.$iframe.on("load", function() {
                var data,
                        win,
                        doc;

                try {
                    win = this.contentWindow;
                    doc = this.contentDocument;

                    doc = doc ? doc : win.document;
                    data = doc ? doc.body.innerText : null;
                } catch (e) {
                }

                if (data) {
                    _this.submitDone(data);
                } else {
                    if (firstLoad) {
                        firstLoad = false;
                    } else {
                        _this.submitFail("Image upload failed!");
                    }
                }

                _this.submitEnd();
            });
        },
        click: function() {
            this.$eventPictureModal.modal("show");
        },
        change: function() {
            console.log("change");
            var files,
                    file;

            if (this.support.datauri) {
                files = this.$eventPictureInput.prop("files");

                if (files.length > 0) {
                    file = files[0];

                    if (this.isImageFile(file)) {
                        this.read(file);
                    }
                }
            } else {
                file = this.$eventPictureInput.val();
                if (this.isImageFile(file)) {
                    this.syncUpload();
                }
            }
        },
        submit: function() {
            if (!this.$eventPictureSrc.val() && !this.$eventPictureInput.val()) {
                return false;
            }

            if (this.support.formData) {
                this.ajaxUpload();
                return false;
            }
        },
        isImageFile: function(file) {
            if (file.type) {
                return /^image\/\w+$/.test(file.type);
            } else {
                return /\.(jpg|jpeg|png|gif)$/.test(file);
            }
        },
        read: function(file) {
            var _this = this,
                    fileReader = new FileReader();

            fileReader.readAsDataURL(file);

            fileReader.onload = function() {
                _this.url = this.result
                _this.startCropper();
            };
        },
        startCropper: function() {
            console.log('start Cropper');
            var _this = this;
            if (this.active) {
                this.$img.cropper("setImgSrc", this.url);
            } else {
                this.$img = $('<img src="' + this.url + '">');
                this.$eventPictureWrapper.empty().html(this.$img);
                this.$img.cropper({
                    aspectRatio: 0.6666,
                    preview: this.$eventPicturePreview.selector,
                    done: function(data) {
                        var json = [
                            '{"x":' + data.x,
                            '"y":' + data.y,
                            '"height":' + data.height,
                            '"width":' + data.width + "}"
                        ].join();

                        _this.$eventPictureData.val(json);
                    }
                });

                this.active = true;
            }
        },
        stopCropper: function() {
            if (this.active) {
                this.$img.cropper("disable");
                this.$img.data("cropper", null).remove();
                this.active = false;
            }
        },
        ajaxUpload: function() {
            var url = this.$eventPictureForm.attr("action"),
                    data = new FormData(this.$eventPictureForm[0]),
                    _this = this;

            $.ajax(url, {
                type: "post",
                data: data,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    _this.submitStart();
                },
                success: function(data) {
                    _this.submitDone(data);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    _this.submitFail(textStatus || errorThrown);
                },
                complete: function() {
                    _this.submitEnd();
                }
            });
        },
        syncUpload: function() {
            this.$eventPictureSave.click();
        },
        submitStart: function() {
            this.$loading.fadeIn();
        },
        submitDone: function(data) {

            try {
                data = $.parseJSON(data);
            } catch (e) {
            }
            ;

            if (data && data.state === 200) {
                if (data.result) {
                    this.url = data.result;

                    if (this.support.datauri || this.uploaded) {
                        this.uploaded = false;
                        this.cropDone();
                    } else {
                        this.uploaded = true;
                        this.$eventPictureSrc.val('/' + this.url);
                        this.startCropper();
                    }

                    this.$eventPictureInput.val("");
                } else if (data.message) {
                    this.alert(data.message);
                }
            } else {
                this.alert("Erreur de téléchargement");
            }
        },
        submitFail: function(msg) {
            this.alert(msg);
        },
        submitEnd: function() {
            this.$loading.fadeOut();
        },
        cropDone: function() {
            this.$eventPictureSrc.val("");
            this.$eventPictureData.val("");
            this.$eventPicture.attr("src", this.url);
            this.stopCropper();
            this.$eventPictureModal.modal("hide");
        },
        alert: function(msg) {
            var $alert = [
                '<div class="alert alert-danger avater-alert">',
                '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                msg,
                '</div>'
            ].join("");

            this.$eventPictureUpload.after($alert);
        }
    };

    $(function() {
        $('.crop-eventPicture').each(function() {
            var example = new CropEventPicture($(this));
        });
    });
});
