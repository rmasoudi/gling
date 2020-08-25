$(document).ready(function () {
    $('#sidePanel').slideReveal({
        trigger: $("#btnCart"),
        push: false,
        overlay: true
    });
    $('#sidePanel').removeClass("hidden");
    loadCart();
});

$(window).scroll(function () {
    if ($(window).scrollTop() + $(window).height() === getDocHeight()) {
        $(".bottomBar").removeClass("bottomBarClosed");
    } else {
        $(".bottomBar").addClass("bottomBarClosed");
    }
});
function loadCart() {
    $("#billRows").html("");
    var products = getProducts();
    var total = 0;
    for (var i = 0; i < products.length; i++) {
        total += addProductRow(products[i], i);
    }
}
function addProductRow(product, index) {
    var template = $("#productTemplate").html();
    var total = getRowPrice(product);
    var compiled = _.template(template);
    var res = compiled({
        title: product.product.title,
        clone: product.clone,
        price: product.product.price,
        index: index,
        total: total
    });
    $("#billRows").append(res);
    return total;
}


function correctPage(page) {
    if (page === "0" || page === 0) {
        return 1;
    }
    return parseInt(page);
}

function editClicked(index) {
    var product = getProducts()[index];
    setEditing(index, product);
    window.location = product.product.url;
}
function removeClicked(index) {
    removeProduct(index);
    $("#cartItem_" + index).remove();
}
function getRowPrice(product) {
    var prices = JSON.parse($("#txtPrice").val());
    var total = 0;
    var translateBase = product.product.price;
    if (product.lang !== "1") {
        translateBase = product.product.extra;
    }
    var pages = parseInt(product.pages);
    if (pages > 1) {
        total = translateBase * pages;
    } else {
        total = translateBase;
    }
    if (product.marriage) {
        total += prices.shenas_item;
    }
    if (product.talagh) {
        total += prices.shenas_item;
    }
    if (product.wifeDie) {
        total += prices.shenas_item;
    }
    if (product.die) {
        total += prices.shenas_item;
    }
    if (product.desc) {
        total += prices.shenas_item;
    }
    total += prices.shenas_item * product.childCount;
    total += prices.passport * (product.mohrCount + product.vizaCount);
    total += prices.enteghal * (product.entghalCount);
    if (product.clone !== "1") {
        total += (parseInt(product.clone) - 1) * total / 4;
    }
    return total;
}
function addProduct(item) {
    var products = getProducts();
    products.push(item);
    setProducts(products);
}

function getProducts() {
    var productsStr = localStorage.getItem("products");
    var products;
    if (productsStr === null || productsStr === undefined) {
        products = [];
    } else {
        products = JSON.parse(productsStr);
    }
    return products;
}
function setProducts(products) {
    localStorage.setItem("products", JSON.stringify(products));
}

function removeProduct(index) {
    var products = getProducts();
    products.splice(index, 1);
    setProducts(products);
}
function editProduct(index, item) {
    var products = getProducts();
    products[index] = item;
    setProducts(products);
}

function setProductUrl(index, pageIndex, url) {
    var products = getProducts();
    var product = products[index];
    var uploads;
    if (product.uploads === null || product.uploads === undefined) {
        uploads = [];
    } else {
        uploads = product.uploads;
    }
    uploads[pageIndex] = url;
    product.uploads = uploads;
    products[index] = product;
    setProducts(products);
}

function isUploadOK() {
    var products = getProducts();
    if (products === null || products === undefined) {
        return false;
    }
    for (var i = 0; i < products.length; i++) {
        if (products[i].uploads === undefined) {
            return false;
        }
        var imageUploads = products[i].uploads;
        if (imageUploads === undefined || imageUploads === null) {
            return false;
        }
        if (products[i].pages !== undefined && products[i].pages !== null) {
            if (products[i].uploads.length < parseInt(products[i].pages)) {
                return false;
            }
        }
    }
    return true;
}

function setEditing(index, product) {
    if (index === -1) { // clear editing item
        localStorage.removeItem("editing");
        return;
    }
    var pair = {
        index: index,
        product: product
    };
    localStorage.setItem("editing", JSON.stringify(pair));
}

function getEditing() {
    var str = localStorage.getItem("editing");
    if (str === null || str === undefined) {
        return null;
    }
    return JSON.parse(localStorage.getItem("editing"));
}

function setAddress(index, item) {
    var obj = {
        index: index,
        item: item
    };
    localStorage.setItem("address", JSON.stringify(obj));
}

function getAddress() {
    if (localStorage.getItem("address") === null) {
        return null;
    }
    return JSON.parse(localStorage.getItem("address"));
}

function setCenter(index, item) {
    var obj = {
        index: index,
        item: item
    };
    localStorage.setItem("center", JSON.stringify(obj));
}

function getCenter() {
    if (localStorage.getItem("center") === null) {
        return null;
    }
    return JSON.parse(localStorage.getItem("center"));
}
function isFreeCarrier(center, dist) {
    if (center.carrier === "0") {
        return false;
    }
    if (center.carrier === "7") {
        return true;
    }
    var range = 0;
    switch (center.carrier) {
        case "1":
            range = 3;
            break;
        case "2":
            range = 6;
            break;
        case "3":
            range = 10;
            break;
        case "4":
            range = 20;
            break;
        case "5":
            range = 30;
            break;
        case "6":
            range = 50;
            break;
    }
    return dist <= range;
}
function getDistance(lat1, lon1, point) {
    array = point.split(",");
    lat2 = parseFloat(array[0]);
    lon2 = parseFloat(array[1]);
    if ((lat1 === lat2) && (lon1 === lon2)) {
        return 0;
    } else {
        var radlat1 = Math.PI * lat1 / 180;
        var radlat2 = Math.PI * lat2 / 180;
        var theta = lon1 - lon2;
        var radtheta = Math.PI * theta / 180;
        var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
        if (dist > 1) {
            dist = 1;
        }
        dist = Math.acos(dist);
        dist = dist * 180 / Math.PI;
        dist = dist * 60 * 1.1515;
        return dist * 1.609344;
    }
}
function getSelectedLang() {
    var products = getProducts();
    var val;
    for (var i = 0; i < products.length; i++) {
        val = products[i].lang;
        break;
    }
    return getLangByCode(val);
}

function getLangByCode(val) {
    switch (val) {
        case "1":
            return "انگلیسی";
        case "2":
            return "فرانسوی";
        case "3":
            return "آلمانی";
        case "4":
            return "اسپانیایی";
        case "5":
            return "چینی";
        case "6":
            return "عربی";
        case "7":
            return "اردو";
        case "8":
            return "ژاپنی";
        case "9":
            return "ترکی استانبولی";
        case "10":
            return "ترکی";
        case "11":
            return "ایتالیایی";
        case "12":
            return "روسی";
        case "13":
            return "ارمنی";
        case "14":
            return "کردی";

    }
    return "";
}

function getDocHeight() {
    var D = document;
    return Math.max(
            D.body.scrollHeight, D.documentElement.scrollHeight,
            D.body.offsetHeight, D.documentElement.offsetHeight,
            D.body.clientHeight, D.documentElement.clientHeight
            );
}

function goNext(next) {
    if (next === 2) {
        var products = getProducts();
        if (products.length === 0) {
            $.notify(
                    "ابتدا نوع سند را انتخاب کنید.",
                    {position: "top center"}
            );
            return;
        }
        window.location = "upload";
    } else if (next === 3) {
        if (isUploadOK()) {
            window.location = "address";
        } else {
            $.notify(
                    "لطفا مدارک لازم را پیوست کنید",
                    {position: "b"}
            );
        }
    } else if (next === 4) {
        if (getAddress() !== null && getAddress().item!==undefined) {
            window.location = "centers_step";
        } else {
            $.notify(
                    "لطفا آدرس را تعیین کنید",
                    {position: "b"}
            );
        }
    } else if (next === 5) {
        if (getCenter() !== null && getCenter().item!==undefined) {
            window.location = "bill";
        } else {
            $.notify(
                    "لطفا مترجم را انتخاب کنید",
                    {position: "b"}
            );
        }
    }
}