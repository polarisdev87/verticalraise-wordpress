

function resizeCrop(src, width, height, orientation) {
    var crop = width == 0 || height == 0;
    // not resize
    if (src.width <= width && height == 0) {
        width = src.width;
        height = src.height;
    }
    // resize
    if (src.width > width && height == 0) {
        height = src.height * (width / src.width);
    }

    // check scale
    var xscale = width / src.width;
    var yscale = height / src.height;
    var scale = crop ? Math.min(xscale, yscale) : Math.max(xscale, yscale);
    // create empty canvas
    var canvas = document.createElement("canvas");
    canvas.width = width ? width : Math.round(src.width * scale);
    canvas.height = height ? height : Math.round(src.height * scale);

    if (!orientation || orientation > 8) {
        return
    }
    switch (orientation) {
        case 2:
            // horizontal flip
            canvas.getContext("2d").translate(width, 0)
            canvas.getContext("2d").scale(-1, 1)
            break
        case 3:
            // 180° rotate left
            canvas.getContext("2d").translate(width, height)
            canvas.getContext("2d").rotate(Math.PI)
            break
        case 4:
            // vertical flip
            canvas.getContext("2d").translate(0, height)
            canvas.getContext("2d").scale(1, -1)
            break
        case 5:
            // vertical flip + 90 rotate right
            canvas.getContext("2d").rotate(0.5 * Math.PI)
            canvas.getContext("2d").scale(1, -1)
            break
        case 6:
            // 90° rotate right
            canvas.getContext("2d").rotate(0.5 * Math.PI)
            canvas.getContext("2d").translate(0, -height)
            break
        case 7:
            // horizontal flip + 90 rotate right
            canvas.getContext("2d").rotate(0.5 * Math.PI)
            canvas.getContext("2d").translate(width, -height)
            canvas.getContext("2d").scale(-1, 1)
            break
        case 8:
            // 90° rotate left
            canvas.getContext("2d").rotate(-0.5 * Math.PI)
            canvas.getContext("2d").translate(-width, 0)
            break
    }

    canvas.getContext("2d").scale(scale, scale);

    // crop it top center
    canvas.getContext("2d").drawImage(src, ((src.width * scale) - canvas.width) * -.5, ((src.height * scale) - canvas.height) * -.5);
    return canvas;
}

function createObjectURL(i) {
    var URL = window.URL || window.webkitURL || window.mozURL || window.msURL;
    return URL.createObjectURL(i);
}


//
function getOrientation(file, callback) {
    var reader = new FileReader();
    reader.onload = function(e) {

        var view = new DataView(e.target.result);
        if (view.getUint16(0, false) != 0xFFD8)
        {
            return callback(-2);
        }
        var length = view.byteLength, offset = 2;
        while (offset < length)
        {
            if (view.getUint16(offset+2, false) <= 8) return callback(-1);
            var marker = view.getUint16(offset, false);
            offset += 2;
            if (marker == 0xFFE1)
            {
                if (view.getUint32(offset += 2, false) != 0x45786966)
                {
                    return callback(-1);
                }

                var little = view.getUint16(offset += 6, false) == 0x4949;
                offset += view.getUint32(offset + 4, little);
                var tags = view.getUint16(offset, little);
                offset += 2;
                for (var i = 0; i < tags; i++)
                {
                    if (view.getUint16(offset + (i * 12), little) == 0x0112)
                    {
                        return callback(view.getUint16(offset + (i * 12) + 8, little));
                    }
                }
            }
            else if ((marker & 0xFF00) != 0xFF00)
            {
                break;
            }
            else
            {
                offset += view.getUint16(offset, false);
            }
        }
        return callback(-1);
    };
    reader.readAsArrayBuffer(file);
}