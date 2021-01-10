/*
 
 */
var _token = false;

var signinCallback = function (result) {

    if (result.access_token) {
        _token = result.access_token;
        if ($('#fundvideoFile').val()) {

            var uploadVideo = new UploadVideo();
            uploadVideo.ready(result.access_token);
        }
    }
};

var STATUS_POLLING_INTERVAL_MILLIS = 5 * 1000; // One minute.

/**
 * YouTube video uploader class
 *
 * @constructor
 */
var UploadVideo = function () {
    /**
     * The array of tags for the new YouTube video.
     *
     * @attribute tags
     * @type Array.<string>
     * @default ['google-cors-upload']
     */
    this.tags = ['youtube-cors-upload'];

    /**
     * The numeric YouTube
     * [category id](https://developers.google.com/apis-explorer/#p/youtube/v3/youtube.videoCategories.list?part=snippet&regionCode=us).
     *
     * @attribute categoryId
     * @type number
     * @default 22
     */
    this.categoryId = 22;

    /**
     * The id of the new video.
     *
     * @attribute videoId
     * @type string
     * @default ''
     */
    this.videoId = '';
    this.count = 0;
    this.uploadStartTime = 0;
};


UploadVideo.prototype.ready = function (accessToken) {

    this.accessToken = accessToken;
    this.gapi = gapi;
    this.authenticated = true;
    this.gapi.client.request({
        path: '/youtube/v3/channels',
        params: {
            part: 'snippet',
            mine: true
        },
        headers: {
            Authorization: 'Bearer ' + accessToken
        },
        callback: function (response) {

            if (response.error) {

                console.log(response.error);
            } else {
                // $('#button').trigger("click");
                $('#button').css('display', 'inline-block');
                $('#button').trigger("click");
            }
        }.bind(this)
    });
    $('#button').off("click");
    $('#button').on("click", this.handleUploadClicked.bind(this));
};



/**
 * Uploads a video file to YouTube.
 *
 * @method uploadFile
 * @param {object} file File object corresponding to the video to upload.
 */
UploadVideo.prototype.uploadFile = function (file) {
    // $("#video_width").val()
    // $("#video_height").val()
    var metadata = {
        snippet: {
            title: $('#title').val(),
            description: $('#description').text(),
            tags: this.tags,
            categoryId: this.categoryId,

            fileDetails: {
                videoStreams: [
                    {
                        widthPixels: $("#video_width").val(),
                        heightPixels: $("#video_height").val()
                    }
                ]
            }

        },
        status: {
            privacyStatus: "unlisted"
        }
    };
    var uploader = new MediaUploader({

        baseUrl: 'https://www.googleapis.com/upload/youtube/v3/videos',
        file: file,
        token: _token,
        metadata: metadata,
        params: {
            part: Object.keys(metadata).join(',')
        },
        onError: function (data) {
            var message = data;
            // Assuming the error is raised by the YouTube API, data will be
            // a JSON string with error.message set. That may not be the
            // only time onError will be raised, though.
            try {
                console.log(data)
                console.log(_token)
                var errorResponse = JSON.parse(data);
                message = errorResponse.error.message;
            } finally {
                alert(message);
            }
        }.bind(this),
        onProgress: function (data) {
            $('#post-upload-status').text('');
            $('.during-upload').css('display', 'block');
            var currentTime = Date.now();
            var bytesUploaded = data.loaded;
            var totalBytes = data.total;
            // The times are in millis, so we need to divide by 1000 to get seconds.
            var bytesPerSecond = bytesUploaded / ((currentTime - this.uploadStartTime) / 1000);
            var estimatedSecondsRemaining = (totalBytes - bytesUploaded) / bytesPerSecond;
            var percentageComplete = (bytesUploaded * 100) / totalBytes;


            $('#upload-progress').attr({
                value: bytesUploaded,
                max: totalBytes
            });

            $('#percent-transferred').text(percentageComplete.toFixed(2));
            $('#bytes-transferred').text(bytesUploaded);
            $('#total-bytes').text(totalBytes);

        }.bind(this),
        onComplete: function (data) {
            $('.during-upload').css('display', 'none');
            $('#loading').show();
            $('#fundvideoFile').val('');
            var uploadResponse = JSON.parse(data);
            this.videoId = uploadResponse.id;

//            $("#youtube_link").val('https://youtu.be/' + this.videoId);

            this.pollForVideoStatus();
        }.bind(this)
    });
    // This won't correspond to the *exact* start of the upload, but it should be close enough.
    this.uploadStartTime = Date.now();
    uploader.upload();
};

UploadVideo.prototype.handleUploadClicked = function () {
    $('#button').css('display', 'none');
    this.uploadFile($('#fundvideoFile').get(0).files[0]);

};

UploadVideo.prototype.pollForVideoStatus = function () {
    this.gapi.client.request({
        path: 'https://www.googleapis.com/youtube/v3/videos',
        params: {
            part: 'status, player',
            id: this.videoId
        },
        headers: {
            Authorization: 'Bearer ' + this.accessToken
        },
        callback: function (response) {
            console.log("response", response);
            if (response.error) {
                // The status polling failed.
                console.log(response.error.message);
//                setTimeout(this.pollForVideoStatus.bind(this), STATUS_POLLING_INTERVAL_MILLIS);
            } else {
                if (response.items.length == 0) {
                    if (this.count < 3) {
                        this.count++;
                        setTimeout(this.pollForVideoStatus.bind(this), STATUS_POLLING_INTERVAL_MILLIS);
                    } else {
                        this.count = 0;
                        $('#loading').hide();
                        $('#post-upload-status').removeClass('fail').removeClass('success').addClass('fail');
                        $('#post-upload-status').text('Transcoding failed.');
                        console.log("fail")
                    }
//                    return false;
                } else {
                    var uploadStatus = response.items[0].status.uploadStatus;
                    switch (uploadStatus) {
                        // This is a non-final status, so we need to poll again.
                        case 'uploaded':
//                        $('#post-upload-status').append('<li>Upload status: ' + uploadStatus + '</li>');
                            setTimeout(this.pollForVideoStatus.bind(this), STATUS_POLLING_INTERVAL_MILLIS);
                            break;
                            // The video was successfully transcoded and is available.
                        case 'processed':
                            this.count = 0;
                            $('#loading').hide();
                            $('#post-upload-status').removeClass('fail').removeClass('success').addClass('success');
                            $("#youtube_link").val('https://youtu.be/' + this.videoId);
                            $('#post-upload-status').text('Processed Successfully.');
                            break;
                            // All other statuses indicate a permanent transcoding failure.
                        case 'rejected':
                            this.count = 0;
                            jQuery('#loading').hide();
                            jQuery('#post-upload-status').removeClass('fail').removeClass('success').addClass('fail');
                            jQuery('#post-upload-status').text('This video is rejected (rejectReason: '+response.items[0].status.rejectionReason+').');
                            console.log("fail")
                        default:
                            break;
                    }
                }
            }
        }.bind(this)
    });
};
