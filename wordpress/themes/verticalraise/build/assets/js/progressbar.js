//Desktop
function progressBar(percent, element, totalval, currentval) {
    var elem = element.find("div");
    var nextelem = element.parent().find("div.total_gain.desk");
    if (percent == 0 || percent == null) {
        elem.css("width", '0px');
        elem.text('0%');
        nextelem.css("left", '0%');
    }
    width = 0;
    var id = setInterval(frame, 5);
    function frame() {
        width += 2;
        var maxwidth = percent * element.width() / 100;
        var pc = 0;
        var progress_val = 0;
        if (width > maxwidth) {
            clearInterval(id)
            elem.css("width", maxwidth + 'px');
            elem.text(percent + '%');
            nextelem.css("left", percent + '%')
            nextelem.find("h5 span").text("$" + comma(currentval))

        } else {
            pc = width / element.width() * 100;
            progress_val = Math.round(totalval * pc / 100)
            elem.css("width", width + 'px');
            elem.text(pc + '%');
            nextelem.css("left", pc + '%');
            nextelem.css("display", "inherit");
            nextelem.find("h5 span").text('$ ' + comma(progress_val));
        }
    }
}

//for green progress bar =
function progressBar1(percent, element, totalval, currentval) {
    var elem = element.find("div");
    var nextelem = element.parent().find("div.total_gain.mob");
    if (percent == 0 || percent == null) {
        elem.css("width", '0px');
        elem.text('0%');
    }
    width = 0;
    var id = setInterval(frame, 5);
    // currentval.toFixed(0).replace(/(\d)(?=(\d{3})+\.)/g, ',')
    function frame() {
        width += 3;
        var maxwidth = percent * element.width() / 100;
        var pc = 0;
        var progress_val = 0;
        if (width > maxwidth) {
            clearInterval(id)
            elem.css("width", maxwidth + 'px');
            elem.text(percent + '%');
            if (currentval != null) {
                nextelem.find("h5 span").text("$" + comma(currentval))
            }
        } else {
            pc = width / element.width() * 100;
            elem.text(pc + '%');
            elem.css("width", width + 'px');
            if (totalval != null) {
                progress_val = Math.round(totalval * pc / 100)
                nextelem.css("display", "inherit")
                nextelem.find("h5 span").text('$ ' + comma(progress_val))
            }
        }
    }
}
function comma(num) {
    var parts = num.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
}