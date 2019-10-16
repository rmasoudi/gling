
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
    }
    else {
        products = JSON.parse(productsStr);
    }
    return products;
}
function setProducts(products) {
    localStorage.setItem("products", JSON.stringify(products));
}

function removeProduct(index){
    var products=getProducts();
    products.splice(index,1);
    setProducts(products);
}
function editProduct(index,item){
    var products=getProducts();
    products[index]=item;
    setProducts(products);
}

function setEditing(index,product){
    if(index===-1){ // clear editing item
        localStorage.removeItem("editing");
        return;
    }
    var pair={
        index:index,
        product:product
    };
    localStorage.setItem("editing",JSON.stringify(pair));
}

function getEditing(){
    var str=localStorage.getItem("editing");
    if(str===null || str===undefined){
        return null;
    }
    return JSON.parse(localStorage.getItem("editing"));
}