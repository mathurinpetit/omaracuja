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

    function CropPicture($element) {
        this.$container = $element;
        this.$pictureDefaultView = this.$container.find(".picture-default-view");
        this.$pictureLink = this.$container.find(".picture-link");
        this.$picture = this.$pictureDefaultView.find("img");
        this.$pictureModal = this.$container.find("#picture-modal");
        this.$loading = this.$container.find(".loading");

        this.$pictureForm = this.$pictureModal.find(".picture-form");
        this.$pictureUpload = this.$pictureForm.find(".picture-upload");
        this.$pictureSrc = this.$pictureForm.find(".picture-src");
        this.$pictureData = this.$pictureForm.find(".picture-data");
        this.$pictureInput = this.$pictureForm.find(".picture-input");
        this.$pictureSave = this.$pictureForm.find(".picture-save");

        this.$pictureWrapper = this.$pictureModal.find(".picture-wrapper");
        this.$picturePreview = this.$pictureModal.find(".picture-preview");

        this.init();
    }

    CropPicture.prototype = {
        constructor: CropPicture,
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
            this.$pictureLink.on("click", $.proxy(this.click, this));
            this.$pictureInput.on("change", $.proxy(this.change, this));
            this.$pictureForm.on("submit", $.proxy(this.submit, this));
        },
        initTooltip: function() {
            this.$pictureLink.tooltip({
                placement: "bottom"
            });
        },
        initModal: function() {
            this.$pictureModal.modal("hide");
            this.initPreview();
        },
        initPreview: function() {
            var url = this.$picture.attr("src");
            this.$pictureWrapper.empty().html('<div class="cropper-container" style="height: 600px; left: 200px; top: 0px; width: 400px;" ><img src="' + url + '" ></div>');
        },
        initIframe: function() {
            var iframeName = "picture-iframe-" + Math.random().toString().replace(".", ""),
                    $iframe = $('<iframe name="' + iframeName + '" style="display:none;"></iframe>'),
                    firstLoad = true,
                    _this = this;

            this.$iframe = $iframe;
            this.$pictureForm.attr("target", iframeName).after($iframe);

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
            this.$pictureModal.modal("show");
        },
        change: function() {
            console.log("change");
            var files,
                    file;

            if (this.support.datauri) {
                files = this.$pictureInput.prop("files");

                if (files.length > 0) {
                    file = files[0];

                    if (this.isImageFile(file)) {
                        this.read(file);
                    }
                }
            } else {
                file = this.$pictureInput.val();
                if (this.isImageFile(file)) {
                    this.syncUpload();
                }
            }
        },
        submit: function() {
            if (!this.$pictureSrc.val() && !this.$pictureInput.val()) {
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
                this.$pictureWrapper.empty().html(this.$img);
                this.$img.cropper({
                    preview: this.$picturePreview.selector,
                    done: function(data) {
                        var json = [
                            '{"x":' + data.x,
                            '"y":' + data.y,
                            '"height":' + data.height,
                            '"width":' + data.width + "}"
                        ].join();

                        _this.$pictureData.val(json);
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
            var url = this.$pictureForm.attr("action"),
                    data = new FormData(this.$pictureForm[0]),
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
            this.$pictureSave.click();
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
                        this.$pictureSrc.val('/' + this.url);
                        this.startCropper();
                    }

                    this.$pictureInput.val("");
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
            this.$pictureSrc.val("");
            this.$pictureData.val("");
            this.$picture.attr("src", this.url);
            this.stopCropper();
            this.$pictureModal.modal("hide");
            $("#new-picture-place").after("<div class=\"picture-view col-lg-3 col-md-4 col-xs-6 thumb\">"+
                        "<a href=\"#\" class=\"thumbnail\">"+
                    "<div class=\"img-with-title\">"+
                            "<span class=\"caption\">jfojzeggsdgdfg</span>"+
                            "<img class=\"img-responsive\" alt=\"jfojzeggsdgdfg\" src=\""+this.url+"\">"+
                        "</div>"+
                        "</a>"+
                    "</div>")
        },
        alert: function(msg) {
            var $alert = [
                '<div class="alert alert-danger avater-alert">',
                '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                msg,
                '</div>'
            ].join("");

            this.$pictureUpload.after($alert);
        }
    };

    $(function() {
        var example = new CropPicture($("#crop-picture"));  
    });
});