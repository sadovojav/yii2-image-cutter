/**
 * Uses cropper javascript widget from https://github.com/fengyuanchen/cropper
 *
 * Author: Joseba Juaniz
 * Year: 2015
 */
$.fn.cutter = function (options) {
    var inputId = options['inputField'];
    var _jcropOptions = options['jcropOptions'];

    var uploadField = $(this);
    var parent = $(this).parents('.uploadcrop');

    var divImageContainer = $(parent).find('div#image-source');

    var imageField = $(divImageContainer).find('img').prop('outerHTML');


    var imageID = $(divImageContainer).find('img').attr('id');
    var x_hidden = $('#' + inputId + '-x');
    var width_hidden = $('#' + inputId + '-width');
    var y_hidden = $('#' + inputId + '-y');
    var height_hidden = $('#' + inputId + '-height');

    var modal = $(parent).find('div.modal');

    var $pcimg = $(parent).find('#preview-pane div.preview-container img.preview_image');

    uploadField.change(function (e) {

        var file = e.target.files[0],
            imageType = /image.*/;

        if (!file.type.match(imageType))
            return;

        var reader = new FileReader();
        reader.onload = fileOnload;
        reader.readAsDataURL(file);
    });

    function fileOnload(e) {
        // basic initializations

        $(divImageContainer).html('').append(imageField);

        $('#' + imageID).prop('src', e.target.result.toString()).hide();

        $(x_hidden).prop('disabled', false);
        $(width_hidden).prop('disabled', false);
        $(y_hidden).prop('disabled', false);
        $(height_hidden).prop('disabled', false);

        $('#' + imageID + '_button_accept').off('click');
        $('#' + imageID + '_button_cancel').off('click');
        $(modal).off('shown.bs.modal');

        // get crop data and put it in the preview image
        $('#' + imageID + '_button_accept').on('click', function () {

            // if there is no selected area, then all the image will be uploaded
            // with its real size as cropping data
            var croppedImage = $('#' + imageID).cropper('getDataURL');

            if (croppedImage == '') {
                var sizes = $('#' + imageID).cropper("getImageData");

                // we will crop by the size of the image, so no real cropping will be made
                $(x_hidden).val('0');
                $(width_hidden).val(sizes.naturalWidth);
                $(y_hidden).val('0');
                $(height_hidden).val(sizes.naturalHeight);

                // we will put all the image as preview
                $pcimg.prop('src', $('#' + imageID).prop('src'));
            }
            else {
                $pcimg.prop('src', croppedImage);
            }

            $('#preview-pane').show();

            $(divImageContainer).html('');
            $(modal).modal('hide');
        });

        // if cancel, the image won't be cropped
        $('#' + imageID + '_button_cancel').on('click', function () {
            // reset all the values
            $(uploadField).val('');

            $pcimg.hide();
            $(divImageContainer).html('');

            $(x_hidden).val('');
            $(width_hidden).val('');
            $(y_hidden).val('');
            $(height_hidden).val('');

            $(x_hidden).prop('disabled', true);
            $(width_hidden).prop('disabled', true);
            $(y_hidden).prop('disabled', true);
            $(height_hidden).prop('disabled', true);

            $(modal).modal('hide');
        });


        // this wil be launched on modal shown in order to get real widths from the elements
        $(modal).on('shown.bs.modal', function (a) {
            // change the image container size because otherwise the image will
            // grow to fit in its parent and we want smaller images in case they are
            // too big, not oversized versions of them
            if ($(divImageContainer).width() > $('#' + imageID).width()) {
                $(divImageContainer).css({
                    width: $('#' + imageID).width() + 'px'
                });
            }

            // sets the data when dragging
            _jcropOptions['dragend'] = function () {
                var crop = $(this).cropper("getData");

                $(x_hidden).val(crop.x);
                $(width_hidden).val(crop.width);
                $(y_hidden).val(crop.y);
                $(height_hidden).val(crop.height);
            };

            // don't select anything from start, it's ugly
            _jcropOptions['built'] = function () {
                $('#' + imageID).cropper('clear');

                // important in case the image is bigger than the window size
                $(modal).modal('adjustBackdrop');
            };

            // start cropper itself
            $('#' + imageID).cropper(_jcropOptions);
        });

        // Zhu li, do the thing!
        $(modal).modal('show');
    }
}