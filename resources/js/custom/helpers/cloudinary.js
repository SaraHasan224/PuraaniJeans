App.cloudinary = {
    upload: function (fileName, publicFlagId) {
        const res = cloudinary.uploader.upload(fileName, {public_id: publicFlagId});
        return res.then((data) => {
                console.log(data);
                console.log(data.secure_url);
            }).catch((err) => {
                    console.log(err);
            });
    },
    generate: function (publicFlagId, width, height) {
        return cloudinary.url(publicFlagId, {
            width: width,
            height: height
        });
    },
}
