/**
 * Auto-compresses images picked in <input type="file" accept="image/*"> fields
 * before the form submits. Resizes to a max dimension of 1600px and re-encodes
 * as WebP (quality 0.8). Falls back silently to the original file whenever the
 * browser lacks support or compression doesn't help.
 */
(function () {
    var MAX_DIMENSION = 1600;
    var OUTPUT_TYPE = 'image/webp';
    var QUALITY = 0.8;

    function isCompressible(file) {
        return !!file && /^image\//.test(file.type) && file.type !== 'image/svg+xml' && file.type !== 'image/gif';
    }

    function compressFile(file) {
        return new Promise(function (resolve) {
            if (!isCompressible(file) || typeof HTMLCanvasElement === 'undefined') {
                resolve(file);
                return;
            }

            var objectUrl = URL.createObjectURL(file);
            var img = new Image();

            img.onload = function () {
                URL.revokeObjectURL(objectUrl);

                var width = img.naturalWidth;
                var height = img.naturalHeight;

                if (width > MAX_DIMENSION || height > MAX_DIMENSION) {
                    if (width >= height) {
                        height = Math.round(height * (MAX_DIMENSION / width));
                        width = MAX_DIMENSION;
                    } else {
                        width = Math.round(width * (MAX_DIMENSION / height));
                        height = MAX_DIMENSION;
                    }
                }

                try {
                    var canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob(function (blob) {
                        if (!blob || blob.size <= 0 || blob.size >= file.size) {
                            resolve(file);
                            return;
                        }
                        try {
                            var newName = file.name.replace(/\.[^./\\]+$/, '') + '.webp';
                            resolve(new File([blob], newName, { type: OUTPUT_TYPE, lastModified: Date.now() }));
                        } catch (e) {
                            resolve(file);
                        }
                    }, OUTPUT_TYPE, QUALITY);
                } catch (e) {
                    resolve(file);
                }
            };

            img.onerror = function () {
                URL.revokeObjectURL(objectUrl);
                resolve(file);
            };

            img.src = objectUrl;
        });
    }

    function handleChange(e) {
        var input = e.target;
        if (!input.matches || !input.matches('input[type="file"][accept*="image"]')) return;
        if (!input.files || !input.files.length) return;

        var file = input.files[0];
        if (!isCompressible(file)) return;

        input.dataset.compressing = '1';
        input._compressPromise = compressFile(file).then(function (result) {
            if (result !== file) {
                try {
                    var dt = new DataTransfer();
                    dt.items.add(result);
                    input.files = dt.files;
                } catch (e) {
                    // DataTransfer not supported: keep the original file, still fine.
                }
            }
        }).catch(function () {
            // Never block submission on a compression failure.
        }).finally(function () {
            delete input.dataset.compressing;
        });
    }

    function handleSubmit(e) {
        var form = e.target;
        if (!form.querySelectorAll) return;

        var pending = [];
        var fileInputs = form.querySelectorAll('input[type="file"][accept*="image"]');
        for (var i = 0; i < fileInputs.length; i++) {
            var input = fileInputs[i];
            if (input.dataset.compressing && input._compressPromise) {
                pending.push(input._compressPromise);
            }
        }

        if (!pending.length) return;

        e.preventDefault();
        e.stopImmediatePropagation();

        var submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        Promise.all(pending).then(function () {
            if (submitBtn) submitBtn.disabled = false;
            form.submit();
        });
    }

    document.addEventListener('change', handleChange, true);
    document.addEventListener('submit', handleSubmit, true);
})();
