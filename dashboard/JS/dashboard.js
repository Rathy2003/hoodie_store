    const sidebar = document.getElementById('sidebar');
    const back_blur = document.querySelector("#back-blur");

    const chbMenu = document.querySelector("#chb-hamburger-menu");
    chbMenu.addEventListener("click",function(e){
      sidebar.style.cssText = "transform: translateX(0)";
      back_blur.style.cssText = "transform: translateX(0)"; 
      // console.log(e.currentTarget.checked);
    })
    back_blur.addEventListener("click",function(){
      sidebar.style.cssText = "";
      back_blur.style.cssText = ""; 
    })