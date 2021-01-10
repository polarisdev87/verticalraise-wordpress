!function (e) {
    function t() {
        return e("<div/>")
    }

    var o = Math.abs, i = Math.max, n = Math.min, s = Math.round;
    e.imgAreaSelect = function (a, c) {
        function r(e) {
            return e + be.left - Se.left
        }

        function d(e) {
            return e + be.top - Se.top
        }

        function u(e) {
            return e - be.left + Se.left
        }

        function h(e) {
            return e - be.top + Se.top
        }

        function f(e) {
            var t, o = m(e) || e;
            return (t = parseInt(o.pageX)) ? t - Se.left : void 0
        }

        function l(e) {
            var t, o = m(e) || e;
            return (t = parseInt(o.pageY)) ? t - Se.top : void 0
        }

        function m(e) {
            var t = e.originalEvent || {};
            return t.touches && t.touches.length ? t.touches[0] : !1
        }

        function p(e) {
            var t = e || G, o = e || J;
            return {
                x1: s(Ae.x1 * t),
                y1: s(Ae.y1 * o),
                x2: s(Ae.x2 * t) - 1,
                y2: s(Ae.y2 * o) - 1,
                width: s(Ae.x2 * t) - s(Ae.x1 * t),
                height: s(Ae.y2 * o) - s(Ae.y1 * o)
            }
        }

        function v(e, t, o, i, n) {
            var a = n || G, c = n || J;
            Ae = {
                x1: s(e / a || 0),
                y1: s(t / c || 0),
                x2: s(++o / a || 0),
                y2: s(++i / c || 0)
            }, Ae.width = Ae.x2 - Ae.x1, Ae.height = Ae.y2 - Ae.y1
        }

        function y() {
            $ && pe.width() && (be = {
                left: s(pe.offset().left),
                top: s(pe.offset().top)
            }, Q = pe.innerWidth(), R = pe.innerHeight(), be.top += pe.outerHeight() - R >> 1, be.left += pe.outerWidth() - Q >> 1, V = s(c.minWidth / G) || 0, Z = s(c.minHeight / J) || 0, _ = s(n(c.maxWidth / G || 1 << 24, Q)), ee = s(n(c.maxHeight / J || 1 << 24, R)), Se = "fixed" == ke ? {
                left: e(document).scrollLeft(),
                top: e(document).scrollTop()
            } : /static|^$/.test(X.css("position")) ? {left: 0, top: 0} : {
                left: s(X.offset().left) - X.scrollLeft(),
                top: s(X.offset().top) - X.scrollTop()
            }, L = r(0), j = d(0), (Ae.x2 > Q || Ae.y2 > R) && P())
        }

        function g(t) {
            if (oe) {
                switch (ve.css({
                    left: r(Ae.x1),
                    top: d(Ae.y1)
                }).add(ye).width(fe = Ae.width).height(le = Ae.height), ye.add(ge).add(we).css({
                    left: 0,
                    top: 0
                }), ge.width(i(fe - ge.outerWidth() + ge.innerWidth(), 0)).height(i(le - ge.outerHeight() + ge.innerHeight(), 0)), e(xe[0]).css({
                    left: L,
                    top: j,
                    width: Ae.x1,
                    height: R
                }), e(xe[1]).css({left: L + Ae.x1, top: j, width: fe, height: Ae.y1}), e(xe[2]).css({
                    left: L + Ae.x2,
                    top: j,
                    width: Q - Ae.x2,
                    height: R
                }), e(xe[3]).css({
                    left: L + Ae.x1,
                    top: j + Ae.y2,
                    width: fe,
                    height: R - Ae.y2
                }), fe -= we.outerWidth(), le -= we.outerHeight(), we.length) {
                    case 8:
                        e(we[4]).css({left: fe >> 1}), e(we[5]).css({
                            left: fe,
                            top: le >> 1
                        }), e(we[6]).css({left: fe >> 1, top: le}), e(we[7]).css({top: le >> 1});
                    case 4:
                        we.slice(1, 3).css({left: fe}), we.slice(2, 4).css({top: le})
                }
                t !== !1 && (e.imgAreaSelect.keyPress != Pe && e(document).unbind(e.imgAreaSelect.keyPress, e.imgAreaSelect.onKeyPress), c.keys && e(document)[e.imgAreaSelect.keyPress](e.imgAreaSelect.onKeyPress = Pe))
            }
        }

        function x(e) {
            y(), g(e), ie = r(Ae.x1), ne = d(Ae.y1), se = r(Ae.x2), ae = d(Ae.y2)
        }

        function w(e, t) {
            c.fadeDuration ? e.fadeOut(c.fadeDuration, t) : e.hide()
        }

        function b(e) {
            return ce && !/^touch/.test(e.type)
        }

        function S(e) {
            var t = u(f(e)) - Ae.x1, o = h(l(e)) - Ae.y1;
            U = "", c.resizable && (o <= c.resizeMargin ? U = "n" : o >= Ae.height - c.resizeMargin && (U = "s"), t <= c.resizeMargin ? U += "w" : t >= Ae.width - c.resizeMargin && (U += "e")), ve.css("cursor", U ? U + "-resize" : c.movable ? "move" : "")
        }

        function z(e) {
            b(e) || (me || (y(), me = !0, ve.one("mouseout", function () {
                me = !1
            })), S(e))
        }

        function k(t) {
            ce = !1, e("body").css("cursor", ""), (c.autoHide || Ae.width * Ae.height == 0) && w(ve.add(xe), function () {
                e(this).hide()
            }), e(document).off("mousemove touchmove", N), ve.on("mousemove touchmove", z), t && c.onSelectEnd(a, p())
        }

        function A(t) {
            return "mousedown" == t.type && 1 != t.which ? !1 : ("touchstart" == t.type ? (ce && k(), ce = !0, S(t)) : y(), U ? (ie = r(Ae["x" + (1 + /w/.test(U))]), ne = d(Ae["y" + (1 + /n/.test(U))]), se = r(Ae["x" + (1 + !/w/.test(U))]), ae = d(Ae["y" + (1 + !/n/.test(U))]), B = se - f(t), F = ae - l(t), e(document).on("mousemove touchmove", N).one("mouseup touchend", k), ve.off("mousemove touchmove", z)) : c.movable ? (Y = L + Ae.x1 - f(t), q = j + Ae.y1 - l(t), ve.off("mousemove touchmove", z), e(document).on("mousemove touchmove", H).one("mouseup touchend", function () {
                ce = !1, c.onSelectEnd(a, p()), e(document).off("mousemove touchmove", H), ve.on("mousemove touchmove", z)
            })) : pe.mousedown(t), !1)
        }

        function I(e) {
            te && (e ? (se = i(L, n(L + Q, ie + o(ae - ne) * te * (se > ie || -1))), ae = s(i(j, n(j + R, ne + o(se - ie) / te * (ae > ne || -1)))), se = s(se)) : (ae = i(j, n(j + R, ne + o(se - ie) / te * (ae > ne || -1))), se = s(i(L, n(L + Q, ie + o(ae - ne) * te * (se > ie || -1)))), ae = s(ae)))
        }

        function P() {
            ie = n(ie, L + Q), ne = n(ne, j + R), o(se - ie) < V && (se = ie - V * (ie > se || -1), L > se ? ie = L + V : se > L + Q && (ie = L + Q - V)), o(ae - ne) < Z && (ae = ne - Z * (ne > ae || -1), j > ae ? ne = j + Z : ae > j + R && (ne = j + R - Z)), se = i(L, n(se, L + Q)), ae = i(j, n(ae, j + R)), I(o(se - ie) < o(ae - ne) * te), o(se - ie) > _ && (se = ie - _ * (ie > se || -1), I()), o(ae - ne) > ee && (ae = ne - ee * (ne > ae || -1), I(!0)), Ae = {
                x1: u(n(ie, se)),
                x2: u(i(ie, se)),
                y1: h(n(ne, ae)),
                y2: h(i(ne, ae)),
                width: o(se - ie),
                height: o(ae - ne)
            }
        }

        function K() {
            P(), g(), c.onSelectChange(a, p())
        }

        function N(e) {
            return b(e) ? void 0 : (P(), se = /w|e|^$/.test(U) || te ? f(e) + B : r(Ae.x2), ae = /n|s|^$/.test(U) || te ? l(e) + F : d(Ae.y2), K(), !1)
        }

        function C(t, o) {
            se = (ie = t) + Ae.width, ae = (ne = o) + Ae.height, e.extend(Ae, {
                x1: u(ie),
                y1: h(ne),
                x2: u(se),
                y2: h(ae)
            }), g(), c.onSelectChange(a, p())
        }

        function H(e) {
            return b(e) ? void 0 : (ie = i(L, n(Y + f(e), L + Q - Ae.width)), ne = i(j, n(q + l(e), j + R - Ae.height)), C(ie, ne), e.preventDefault(), !1)
        }

        function M() {
            e(document).off("mousemove touchmove", M), y(), se = ie, ae = ne, K(), U = "", xe.is(":visible") || ve.add(xe).hide().fadeIn(c.fadeDuration || 0), oe = !0, e(document).off("mouseup touchend", W).on("mousemove touchmove", N).one("mouseup touchend", k), ve.off("mousemove touchmove", z), c.onSelectStart(a, p())
        }

        function W() {
            e(document).off("mousemove touchmove", M).off("mouseup touchend", W), w(ve.add(xe)), v(u(ie), h(ne), u(ie), h(ne)), this instanceof e.imgAreaSelect || (c.onSelectChange(a, p()), c.onSelectEnd(a, p()))
        }

        function D(t) {
            return "mousedown" == t.type && 1 != t.which || xe.is(":animated") ? !1 : (ce = "touchstart" == t.type, y(), Y = ie = f(t), q = ne = l(t), B = F = 0, e(document).on({
                "mousemove touchmove": M,
                "mouseup touchend": W
            }), !1)
        }

        function E() {
            x(!1)
        }

        function O() {
            $ = !0, T(c = e.extend({
                classPrefix: "imgareaselect",
                movable: !0,
                parent: "body",
                resizable: !0,
                resizeMargin: 10,
                onInit: function () {
                },
                onSelectStart: function () {
                },
                onSelectChange: function () {
                },
                onSelectEnd: function () {
                }
            }, c)), ve.add(xe).css({visibility: ""}), c.show && (oe = !0, y(), g(), ve.add(xe).hide().fadeIn(c.fadeDuration || 0)), setTimeout(function () {
                c.onInit(a, p())
            }, 0)
        }

        function T(o) {
            if (o.parent && (X = e(o.parent)).append(ve).append(xe), e.extend(c, o), y(), null != o.handles) {
                for (we.remove(), we = e([]), ue = o.handles ? "corners" == o.handles ? 4 : 8 : 0; ue--;)we = we.add(t());
                we.addClass(c.classPrefix + "-handle").css({
                    position: "absolute",
                    fontSize: 0,
                    zIndex: ze + 1 || 1
                }), !parseInt(we.css("width")) >= 0 && we.width(5).height(5)
            }
            for (G = c.imageWidth / Q || 1, J = c.imageHeight / R || 1, null != o.x1 && (v(o.x1, o.y1, o.x2, o.y2), o.show = !o.hide), o.keys && (c.keys = e.extend({
                shift: 1,
                ctrl: "resize"
            }, o.keys)), xe.addClass(c.classPrefix + "-outer"), ye.addClass(c.classPrefix + "-selection"), ue = 0; ue++ < 4;)e(ge[ue - 1]).addClass(c.classPrefix + "-border" + ue);
            ve.append(ye.add(ge)).append(we), Ke && ((he = (xe.css("filter") || "").match(/opacity=(\d+)/)) && xe.css("opacity", he[1] / 100), (he = (ge.css("filter") || "").match(/opacity=(\d+)/)) && ge.css("opacity", he[1] / 100)), o.hide ? w(ve.add(xe)) : o.show && $ && (oe = !0, ve.add(xe).fadeIn(c.fadeDuration || 0), x()), te = (de = (c.aspectRatio || "").split(/:/))[0] / de[1], pe.add(xe).off("mousedown touchstart", D), c.disable || c.enable === !1 ? (ve.off({
                "mousemove touchmove": z,
                "mousedown touchstart": A
            }), e(window).off("resize", E)) : ((c.enable || c.disable === !1) && ((c.resizable || c.movable) && ve.on({
                "mousemove touchmove": z,
                "mousedown touchstart": A
            }), e(window).resize(E)), c.persistent || pe.add(xe).on("mousedown touchstart", D)), c.enable = c.disable = void 0
        }

        var $, L, j, Q, R, X, Y, q, B, F, G, J, U, V, Z, _, ee, te, oe, ie, ne, se, ae, ce, re, de, ue, he, fe, le, me,
            pe = e(a), ve = t(), ye = t(), ge = t().add(t()).add(t()).add(t()), xe = t().add(t()).add(t()).add(t()),
            we = e([]), be = {left: 0, top: 0}, Se = {left: 0, top: 0}, ze = 0, ke = "absolute",
            Ae = {x1: 0, y1: 0, x2: 0, y2: 0, width: 0, height: 0}, Ie = navigator.userAgent, Pe = function (e) {
                var t, o, s = c.keys, a = e.keyCode;
                if (t = isNaN(s.alt) || !e.altKey && !e.originalEvent.altKey ? !isNaN(s.ctrl) && e.ctrlKey ? s.ctrl : !isNaN(s.shift) && e.shiftKey ? s.shift : isNaN(s.arrows) ? 10 : s.arrows : s.alt, "resize" == s.arrows || "resize" == s.shift && e.shiftKey || "resize" == s.ctrl && e.ctrlKey || "resize" == s.alt && (e.altKey || e.originalEvent.altKey)) {
                    switch (a) {
                        case 37:
                            t = -t;
                        case 39:
                            o = i(ie, se), ie = n(ie, se), se = i(o + t, ie), I();
                            break;
                        case 38:
                            t = -t;
                        case 40:
                            o = i(ne, ae), ne = n(ne, ae), ae = i(o + t, ne), I(!0);
                            break;
                        default:
                            return
                    }
                    K()
                } else switch (ie = n(ie, se), ne = n(ne, ae), a) {
                    case 37:
                        C(i(ie - t, L), ne);
                        break;
                    case 38:
                        C(ie, i(ne - t, j));
                        break;
                    case 39:
                        C(ie + n(t, Q - u(se)), ne);
                        break;
                    case 40:
                        C(ie, ne + n(t, R - h(ae)));
                        break;
                    default:
                        return
                }
                return !1
            };
        this.remove = function () {
            T({disable: !0}), ve.add(xe).remove()
        }, this.getOptions = function () {
            return c
        }, this.setOptions = T, this.getSelection = p, this.setSelection = v, this.cancelSelection = W, this.update = x;
        var Ke = (/msie ([\w.]+)/i.exec(Ie) || [])[1], Ne = /webkit/i.test(Ie) && !/chrome/i.test(Ie);
        for (re = pe; re.length;)ze = i(ze, isNaN(re.css("z-index")) ? ze : re.css("z-index")), c.parent || "fixed" != re.css("position") || (ke = "fixed"), re = re.parent(":not(body)");
        ze = c.zIndex || ze, e.imgAreaSelect.keyPress = Ke || Ne ? "keydown" : "keypress", ve.add(xe).hide().css({
            position: ke,
            overflow: "hidden",
            zIndex: ze || "0"
        }), ve.css({zIndex: ze + 2 || 2}), ye.add(ge).css({
            position: "absolute",
            fontSize: 0
        }), a.complete || "complete" == a.readyState || !pe.is("img") ? O() : pe.one("load", O), !$ && Ke && Ke >= 7 && (a.src = a.src)
    }, e.fn.imgAreaSelect = function (t) {
        return t = t || {}, this.each(function () {
            e(this).data("imgAreaSelect") ? t.remove ? (e(this).data("imgAreaSelect").remove(), e(this).removeData("imgAreaSelect")) : e(this).data("imgAreaSelect").setOptions(t) : t.remove || (void 0 === t.enable && void 0 === t.disable && (t.enable = !0), e(this).data("imgAreaSelect", new e.imgAreaSelect(this, t)))
        }), t.instance ? e(this).data("imgAreaSelect") : this
    }
}(jQuery);