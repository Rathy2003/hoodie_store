const fileEle = document.querySelectorAll("#upload-image-file");
const deletePreviewImage = document.querySelector("#delete-choose-img-btn");
const addProductFrm = document.querySelector("#add-product-frm");
const editProductFrm = document.querySelector("#edit-product-frm");
const searchBox = document.querySelector("#search-bar");

let editImageStatus = null;
let selectedFile = null;



searchBox.addEventListener("keypress",(evt)=>{
  if(evt.key === "Enter")
    document.querySelector("form#search-frm").submit();
})

fileEle.forEach(ele =>{
  ele.addEventListener("change",function(e){
  const file = e.target.files[0];
  const name = file.name;
  const size = file.size;
  const type = file.type;
  
  if(editImageStatus != "delete")
    editImageStatus = "edit";

  const allowType = ["image/jpeg","image/png","image/webp"];

  if(!allowType.includes(type)){
    e.target.value = "";
    if(editImageStatus != "delete")
      editImageStatus = null;
    return alert("File allowed JPG, PNG, WEBP.");
  }

  if(size > 10000000){
    e.target.value = "";
    if(editImageStatus != "delete")
      editImageStatus = null;

    return alert("File size limit 10MB.");
  }

  let container = new DataTransfer(); 
  container.items.add(file);


  selectedFile = file;

  const reader = new FileReader();
  reader.onload = (e)=>{
    const url = e.target.result;
    document.querySelector("#lb-upload-image-file")
      .style.cssText = `background-image: url(${url});background-size: contain;`;
    deletePreviewImage.style.cssText = "visibility: visible;";

    editProductFrm.querySelector("label[for='upload-image-file']")
    .style.cssText = `background-image: url(${url});background-size: contain;`;
  };

  reader.readAsDataURL(file);

  });
})

// function to close modal
function closeModal(modalId){
  const modal = document.getElementById(modalId);
  if(getComputedStyle(modal).display==="flex") { // alternatively: if(modal.classList.contains("modal-show"))
      modal.classList.add("modal-hide");
      setTimeout(() => {
        document.body.style.overflow = "initial";
        modal.classList.remove("modal-show", "modal-hide");
        modal.style.display = "none";      
      }, 200);
  }
}

deletePreviewImage.addEventListener("click",function(e){
  e.stopPropagation();
  deletePreviewImage.style.cssText = "visibility: hidden;";
  document.querySelector("#lb-upload-image-file")
      .style.cssText = `background-image: url('./IMG/upload-image.png')`;
  fileEle.value = "";
});

document.querySelectorAll("input[name='price']").forEach( input => {
  input.addEventListener("input",function(e){
    let value = event.target.value;
    value = value.replace(/[^0-9.]/g, '');
    let decimalCount = (value.match(/\./g) || []).length;
    if (decimalCount > 1) {
      value = value.replace(/\./g, '');
      value = value.replace(/(\d+)(\d{2})$/, '$1.$2');
    }
    event.target.value = value;
  });
})

document.querySelectorAll("input[name='quantity']").forEach( input => {
  input.addEventListener("input",function(e){
      let value = event.target.value;
      value = value.replace(/[^0-9]/g, '');
      if (value.length > 1 && value[0] === '0') {
        value = value.substring(1);
      }
      event.target.value = value;
  });
})

addProductFrm.addEventListener("submit",function(e){
  e.preventDefault();

  const file = this.querySelector("input[type='file']").files[0];
  const name = document.querySelector("#add-product-frm input[name='name']").value.trim();
  const price = document.querySelector("#add-product-frm input[name='price']").value.trim();
  const quantity = document.querySelector("#add-product-frm input[name='quantity']").value.trim();

  if(name == "" || price == "" || quantity == ""){
    return alert("All fields are requried.");
  }

  if(name.search(/^[\w\d\s]+$/)){
    return alert("Product Name must only contain string or number.");
  }

  if(!file){
     return alert("Please upload image file.");
  }

  let formdata = new FormData();

  formdata.append("name",name);
  formdata.append("price",price);
  formdata.append("quantity",quantity);
  formdata.append("file",file);

  // mean all value is valid
  const xhttp = new XMLHttpRequest();
  xhttp.open("POST","./PHP/product.php",true);

  xhttp.onload = function(){
    // console.log(this.responseText);
    const rp = JSON.parse(this.responseText);
    alert(rp.message);
    if(rp.status == 200){
      closeModal(document.querySelector(".modal.modal-show").id);
      addProductFrm.reset();
      window.location.reload();
    }
  }
  xhttp.send(formdata);

});


// delete
document.querySelectorAll(".btn-delete").forEach( btn =>{
  btn.addEventListener("click",function(e){
    const id = e.currentTarget.attributes[1].value;
    if(confirm("Are you sure to delete this product?")){
      let formdata = new FormData();
      formdata.append("id",id);
      const xhttp = new XMLHttpRequest();

      xhttp.onload = function() {
        const rp = JSON.parse(this.responseText);
        alert(rp.message);
        if(rp.status == 200){
           window.location.reload();
        }
      }

      xhttp.open("POST","./PHP/product.php");
      xhttp.send(formdata);
    }
  })
})

// edit
const delete_btn = document.querySelector("#edit-product-frm img#delete-choose-img-btn");
delete_btn.addEventListener("click",function(){
  if(confirm("Are you sure to delete image?")){
    document.querySelector("#edit-product-frm #lb-upload-image-file")
      .style.cssText = `background-image: url('./IMG/upload-image.png')`;
    delete_btn.style.cssText = "visibility: hidden;";
    editImageStatus = "delete";
  }
})

document.querySelectorAll(".btn-edit").forEach(btn =>{
  btn.addEventListener("click",function(evt){
    const data = JSON.parse(evt.currentTarget.attributes[1].value);
    editImageStatus = null;
    document.querySelector("#edit-product-frm input[name='name']").value = data.name;
    document.querySelector("#edit-product-frm input[name='price']").value = data.price;
    document.querySelector("#edit-product-frm input[name='quantity']").value = data.quantity;
    document.querySelector("#edit-product-frm input[name='temp-id']").value = data.id;
    document.querySelector("#edit-product-frm input[name='temp-image']").value = data.image;

    document.querySelector("#edit-product-frm label[for='upload-image-file']")
      .style.cssText = `background-image: url(../IMG/products/${data.image})`;
   
    delete_btn.style.cssText = "visibility: visible;";
  })
})

editProductFrm.addEventListener("submit",function(evt){
  evt.preventDefault();

  const name = this.querySelector("input[name='name']").value.trim();
  const price = this.querySelector("input[name='price']").value.trim();
  const quantity = this.querySelector("input[name='quantity']").value.trim();
  const id = this.querySelector("input[name='temp-id']").value.trim();
  const old_image = this.querySelector("input[name='temp-image']").value.trim();

  if(name == "" || price == "" || quantity == ""){
    return alert("All fields are requried.");
  }

  if(name.search(/^[\w\d\s]+$/)){
    return alert("Product Name must only contain string or number.");
  }

  if(editImageStatus == "delete" || editImageStatus == "edit"){
    if(!selectedFile)
     return alert("Please upload image file.");
  }

  // all valid


  const formdata = new FormData();
  const xhttp = new XMLHttpRequest();
  

  formdata.append("id",id);
  formdata.append("name",name);
  formdata.append("price",price);
  formdata.append("quantity",quantity);
  formdata.append("file",selectedFile);
  formdata.append("old_image",old_image);
  formdata.append("edit_image_status",editImageStatus);

  xhttp.onload = function(){
    const rp = JSON.parse(this.responseText);
    alert(rp.message);
    if(rp.status == 200){
      window.location.reload();
    }
  }

  xhttp.open("POST","./PHP/product.php");
  xhttp.send(formdata);

})

