/*
 * JavaScript Load Image Demo JS
 * https://github.com/blueimp/JavaScript-Load-Image
 *
 * Copyright 2013, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

/* global loadImage, HTMLCanvasElement, $ */

jQuery(function ($) {
    'use strict'

    var result = $('#result')
    var exifNode = $('#exif')
    var thumbNode = $('#thumbnail')
    var actionsNode = $('#actions')
    var currentFile
    var coordinates

    function displayExifData(exif) {
        var thumbnail = exif.get('Thumbnail')
        var tags = exif.getAll()
        var table = exifNode.find('table').empty()
        var row = $('<tr></tr>')
        var cell = $('<td></td>')
        var prop
        if (thumbnail) {
            thumbNode.empty()
            loadImage(thumbnail, function (img) {
                thumbNode.append(img).show()
            }, {orientation: exif.get('Orientation')})
        }
        for (prop in tags) {
            if (tags.hasOwnProperty(prop)) {
                table.append(
                    row.clone()
                        .append(cell.clone().text(prop))
                        .append(cell.clone().text(tags[prop]))
                )
            }
        }
        exifNode.show()
    }

    function updateResults(img, data) {
        var fileName = currentFile.name
        var href = img.src
        var dataURLStart
        var content
        if (!(img.src || img instanceof HTMLCanvasElement)) {
            content = $('<span>Loading image file failed</span>')
        } else {
            if (!href) {
                href = img.toDataURL(currentFile.type + 'REMOVEME')
                // Check if file type is supported for the dataURL export:
                dataURLStart = 'data:' + currentFile.type
                if (href.slice(0, dataURLStart.length) !== dataURLStart) {
                    fileName = fileName.replace(/\.\w+$/, '.png')
                }
            }
            content = $('<div>').append(img)
            //.attr('download', fileName)
            //.attr('href', href)
//            console.log(data)

        }
        result.children().replaceWith(content)
        if (img.getContext) {
            actionsNode.show()
        }
        if (data && data.exif) {
            displayExifData(data.exif)
        }
    }

    function displayImage(file, options) {
        currentFile = file


        console.log("file", file)
        console.log("file1", updateResults)
        console.log("file2", options)

        if (!loadImage(
                file,
                updateResults,
                options
            )) {
            result.children().replaceWith(
                $('<span>' +
                    'Your browser does not support the URL or FileReader API.' +
                    '</span>')
            )
        }
    }

    function dropChangeHandler(e) {
        e.preventDefault()
        e = e.originalEvent
        var target = e.dataTransfer || e.target
        var file = target && target.files && target.files[0]
        /** These are the options **/
        var options = {
            maxWidth: result.width(),
            // maxWidth: 360,
            canvas: true,
            pixelRatio: window.devicePixelRatio,
            downsamplingRatio: 0.5,
            orientation: true,
            contain: false
        }
        if (!file) {
            return
        }
        exifNode.hide()
        thumbNode.hide()
        displayImage(file, options)
    }

    function enableCrop(event) {
        setTimeout(function () {

            var imgNode = result.find('img, canvas')
            var img = imgNode[0]

            //console.log(imgNode);

            var pixelRatio = window.devicePixelRatio || 1
            imgNode.Jcrop({
                aspectRatio: 1,
                setSelect: [
                    40,
                    40,
                    (img.width / pixelRatio) - 40,
                    (img.height / pixelRatio) - 40
                ],
                allowSelect: false,
                onSelect: function (coords) {
                    coordinates = coords
                },
                onRelease: function (coords) {
                    coordinates = coords
                }
            }).parent().on('click', function (event) {
                event.preventDefault()
            })

        }, 100);
    }

    // Get the image size
    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + " Bytes";
        else if (bytes < 1048576) return (bytes / 1024).toFixed(3) + " KB";
        else if (bytes < 1073741824) return (bytes / 1048576).toFixed(3) + " MB";
        else return (bytes / 1073741824).toFixed(3) + " GB";
    }

    // Generate the blob
    function getCanvasBlob(canvas) {
        return new Promise(function (resolve, reject) {
            canvas.toBlob(function (blob) {
                    resolve(blob)
                },
                'image/jpeg', // Output Jpeg
                .8 // Quality to save at)
            )
        })
    }

    // Hide URL/FileReader API requirement message in capable browsers:
    if (window.createObjectURL || window.URL || window.webkitURL ||
        window.FileReader) {
        result.children().hide()
    }

    $(document)
        .on('dragover', function (e) {
            e.preventDefault()
            e = e.originalEvent
            e.dataTransfer.dropEffect = 'copy'
        })
        .on('drop', dropChangeHandler)
        .on('drop', enableCrop)
        .on('drop', displayUploadButton)

    $('#file-input')
        .on('change', dropChangeHandler)
        .on('change', enableCrop)
        .on('change', displayUploadButton)

    $('#crop')
        .on('click', function (event) {
            event.preventDefault()

            // Get the cropped version
            var img = result.find('img, canvas')[0]
            var pixelRatio = window.devicePixelRatio || 1
            if (img && coordinates) {
                updateResults(loadImage.scale(img, {
                    left: coordinates.x * pixelRatio,
                    top: coordinates.y * pixelRatio,
                    sourceWidth: coordinates.w * pixelRatio,
                    sourceHeight: coordinates.h * pixelRatio,
                    minWidth: 300,
                    //maxWidth: result.width(),
                    maxWidth: 400,
                    pixelRatio: pixelRatio,
                    downsamplingRatio: 0.5
                }))
                coordinates = null
            }

            // Slowly increment the progress bar
            function incrementProgressBar(max, speed, multi) {
                var progressBar = $('#progressBar');
                var width = progressBar.width();

                if (width < max) {

                }
            }

            function progressBarInterval(speed) {
                var width = progressBar.width();
                var interval = setInterval(function () {

                    if ($('#progressBar').width() >= max) {
                        clearInterval(interval);
                    }
                    width += 1;
                    if (width <= max) {
                        progressBar.css('width', width + '%');
                        progressBar.text(width + '%');
                    }

                }, getSpeed(speed, multi))

            }

            function getSpeed(speed, multi) {
                var width = $('#progressBar').width();
                if (multi == true) {
                    //console.log('multi is true');
                    if (width > 80) {
                        //console.log('this is met');
                        return 400;
                    } else {
                        return speed;
                    }
                } else {
                    return speed;
                }
            }

            // Convert it to Jpeg
            var canvas = document.getElementById("canvas")
            if (canvas.toBlob) {
                var canvasBlob = getCanvasBlob(canvas)
                canvasBlob.then(function (blob) {
                    // do stuff with blob
                    //console.log(formatBytes(blob.size))
                    //blobUrl = URL.createObjectURL(blob) // URL to the blob
                    //var file = URL.createObjectURL(blob)

                    $('#canvas').hide();

                    ShowLoad();

                    var formData = new FormData;
                    formData.append("blob_file", blob);

                    //check image upload type (Profile image , Team logo image)
                        
                    var upload_type = $("#upload_type").val();
                    
                    var ajax_url = '';
                    if (upload_type == 'profile_image') {
                        ajax_url = '/profile-image-upload-ajax'
                    } else if (upload_type == 'team_logo_image') {
                        ajax_url = '/team-logo-upload-ajax';
                        formData.append("fundraiser_id", $("#f_id").val());
                    }
//                    console.log(ajax_url);return false;
                    $.ajax({

                        xhr: function () {
                            //Get XmlHttpRequest object
                            var xhr = $.ajaxSettings.xhr();
                            //Set onprogress event handler
                            var initial = 1;
                            xhr.upload.onprogress = function (data) {
                                var perc_base = Math.round((data.loaded / data.total) * 100);
                                var perc = perc_base * .80;
                                //console.log('perc_base' + perc_base);
                                //console.log('perc' + perc);

                                if (initial == 1 && perc == 80) {
                                    //incrementProgressBar(100, 10, true);
                                    $('#progressBar').text(perc + '%');
                                    $('#progressBar').css('width', perc + '%');
                                } else if (perc <= 80) {
                                    $('#progressBar').text(perc + '%');
                                    $('#progressBar').css('width', perc + '%');
                                    /*if ( perc >= 80) {
                                     incrementProgressBar(100, 400, false);
                                     }*/
                                } else {
                                    $('#progressBar').text(perc + '%');
                                    $('#progressBar').css('width', perc + '%');
                                    //incrementProgressBar(100, 400, false);
                                }

                                initial = 0;

                                // if it's 100%, do a set time interval to complete it a little bit at a time
                            };
                            return xhr;
                        },

                        type: "POST",
                        url: ajax_url,
                        data: formData,
                        beforeSend: function (request, xhr) {
                            $('#upload-screen').hide();
                        },
                        processData: false,
                        contentType: false,
                        cache: false,
                        timeout: 6000000,
                        success: function (data) {
                            //console.log(JSON.stringify(data));
                            console.log('success',data);
                            if(upload_type == 'team_logo_image'){
                                $("input#uploadurl").val(data);
                            }
                            
                            $("input#uploadsuccess").val('success');
                            
                            $('#progressBar').text('100%');
                            $('#progressBar').css('width', '100%');
                            $('#profile-pic-loader').hide();

                            $('#success-screen').show();
                            reload_original_page();
                        },


                        error: function (e) {
                            console.log(e.statusText);
                            HideLoad();
                            //toastr.error('Error has occurred while uploading the media file.');
                        }
                    });
                }, function (err) {
                    console.log(err)
                });
            }

        })

    function ShowLoad() {
        $('#loading').show();
    }

    function HideLoad() {
        $('#loading').hide();
    }

    function displayUploadButton() {
        $('#upload-button').show();
    }

})