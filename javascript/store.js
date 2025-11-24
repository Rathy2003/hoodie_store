const showSearchModalBtn = document.querySelector("#show-search-modal-btn");
    showSearchModalBtn.addEventListener("click",function(){
        document.querySelector("#search-modal").showModal();
    });

document.querySelector("#search-modal").addEventListener("click",function(e){
    if(e.target.localName == "dialog"){
        document.querySelector("#search-modal").close();
        document.querySelector("#search-modal form").reset();
    }
})

document.addEventListener('keydown', function(e){
    const cartModal =document.getElementById('modal');
    if (e.key == 'Escape' && cartModal.classList.contains('show-modal')){
        closeCart();
    }
})

document.querySelector("#btn-checkout").addEventListener("click",function(){
    closeCart();

    // upload order to database
    const xhttp = new XMLHttpRequest();

    xhttp.onload = function (){
        const rp = JSON.parse(xhttp.responseText);
        if(rp.status === 200){
            let html = `
                <div class="order-detail-wrapper">
                    <span>OrderID</span>
                    <span>#00${rp.data.id}</span>
                </div>
                <div class="order-detail-wrapper">
                    <span>Date</span>
                    <span>${rp.data.date}</span>
                </div>
                <div class="order-detail-wrapper">
                    <span>Discount</span>
                    <span>0%</span>
                </div>
                <div class="order-detail-wrapper">
                    <span>Total Payment</span>
                    <span>${rp.data.total.toFixed(2)}$</span>
                </div>
            
            `;
            document.querySelector("#order-detail-container").innerHTML = html;
            setTimeout(function (){
                document.querySelector("#order-detail-modal").showModal();
            },300);
        }else{
            alert(rp.message);
        }
    }

    xhttp.open("GET","./backend/checkout.php");
    xhttp.send();
})

document.querySelector("#btn-close-order-modal").addEventListener("click",function(){
    document.querySelector("dialog#order-detail-modal").close();
    location.reload();
})


function openCart(){
    const bg = document.getElementById('bg');
    const cartModal =document.getElementById('modal');
    cartModal.classList.add('show-modal');
    bg.style.display = 'block';
}

function closeCart(){
    const bg = document.getElementById('bg');
    const cartModal =document.getElementById('modal');
    cartModal.classList.remove('show-modal');
    bg.style.display = 'none';
}

const bgModal = document.getElementById('bg');
bgModal.addEventListener('click', function(e){
    closeCart();
})


const addToCartBtns = document.querySelectorAll('.add-to-cart');
addToCartBtns.forEach((btn) => {
    btn.addEventListener('click', function(){
        if(loginStatus === "false")
            return alert("Please Login First.");
        const id = this.getAttribute('data-id');
        const title = this.getAttribute('data-title');
        const price = this.getAttribute('data-price');
        const img = this.getAttribute('data-img');
        const key = this.getAttribute('data-key');

        fetch("./backend/addToCart.php", {
            method: "POST",
            headers: {'Content-Type': 'application/json'}, 
            body: JSON.stringify({
                id: id,
                title: title,
                price: price,
                img: img
            })
        })
        .then(res => res.json()) // Expect a JSON response
        .then(data => {
            // console.log("Response from addToCart.php:", data);

            addToCartAnimate(key, img);
            updateCartItemsCount(data.count);
            updateCartItems(data.cartItems);
            updateTotal(data.total);
            // You can use data here
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });
});


function addToCartAnimate(key, img) {
    const productImg = document.getElementById('product-image-'+key);
    const newImg = document.createElement('img');
    const cart = document.getElementById('cart');
    newImg.setAttribute('class', 'temp-img');

    newImg.setAttribute('src',img);
    newImg.classList.add('animate-product');
    newImg.style.zIndex = 12;
    productImg.appendChild(newImg);

    const animatingImg_pos = newImg.getBoundingClientRect();
    const cart_pos = cart.getBoundingClientRect();

    // console.log(animatingImg_pos, cart_pos);

    let data = {
        left : cart_pos.left - (cart_pos.width / 2 + animatingImg_pos.left + animatingImg_pos.width / 2),
        top : cart_pos.top - animatingImg_pos.top - 80
    };

    // console.log(data.left , data.top);

    document.documentElement.style.cssText = `
        --left: ${data.left.toFixed(2)}px;
        --top: ${data.top.toFixed(2)}px;
    `;


    setTimeout(() => {
        newImg.remove();
    }, 1000);

}



function updateCartItemsCount(count){
    const itemCount = document.getElementById('itemCount');
    console.log(count);
    
    if (count > 0){
        itemCount.innerText = count;
    }
    else{
        let cartItems = document.getElementById('cartItems');
        cartItems.innerHTML = "<span class='empty-cart'>Cart is Empty</span>";
        // cartItems.innerHTML = "Cart is Empty";
        itemCount.innerText = '';
    }
}


function updateCartItems(items){
    const cartItems = document.getElementById('cartItems');
    cartItems.innerHTML = '';
    items.forEach((item, index) => {
        cartItems.innerHTML += 
        `
            <div class="cart-item" id="item-${index}">
                <button class="remove-item" onclick="removeItem(${index})">×</button>
                <div class="item-details">
                    <div class="item-image-side">
                        <img src="${item.img}" alt="">
                    </div>
                    <div class="item-info-side">
                        <span class="item-title">${item.name}</span>
                        <span class="item-price">$ ${item.price}</span>
                    </div>
                </div>
                <div class="item-quantity">${item.qty}</div>
            </div>
        `;
    })
}


function updateTotal(total){
    const subTotalPay = document.getElementById('subtotal');
    const totalPay = document.getElementById('total');
    subTotalPay.innerHTML = '$' + total.formatMoney(2, ',', '.');
    // subTotalPay.innerHTML = '$' + Number((total).toFixed(2)).toLocaleString();
    totalPay.innerHTML = '$' + total.formatMoney(2, ',', '.');
}


Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
    var n = this,
        decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSeparator = decSeparator == undefined ? "." : decSeparator,
        thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
        sign = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};


function removeItem(index){
    // console.log(index);
    const item = document.getElementById('item-'+index);

    fetch('./backend/removeCartItem.php',{
        method: 'POST',
        headers: {'Content-Type': 'application/json'}, 
        body: JSON.stringify({
            key: index
        })
    })
    .then(res => res.json()) // Expect a JSON response
    .then(data => {
        console.log("Response from addToCart.php:", data);
        updateCartItems(data.cartItems);
        updateCartItemsCount(data.count);
        updateTotal(data.total);
        item.remove();
        // You can use data here
    })
    .catch(error => {
        console.error("Error:", error);
    });
}
