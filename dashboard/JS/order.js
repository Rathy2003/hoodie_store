const btnViewOrder = document.querySelectorAll(".btn-view");
const dateInput = document.querySelectorAll("input.input-date-range");

dateInput.forEach((ele) => {
  ele.addEventListener("input", function (e) {
    let value = this.value.replace(/[^0-9]/g, "");
    let formattedValue = "";

    if (value.length > 2) {
        formattedValue += value.substring(0, 2) + "-";
        if (value.length > 4) {
            formattedValue += value.substring(2, 4) + "-";
            formattedValue += value.substring(4, 8);
        } else {
            formattedValue += value.substring(2);
        }
    } else {
        formattedValue += value;
    }

    this.value = formattedValue;
  });
});
btnViewOrder.forEach((btn) => {
  btn.addEventListener("click", function (evt) {
    const orderId = btn.getAttribute("data-id");
    const xhttp = new XMLHttpRequest();

    xhttp.onload = function () {
      const rp = JSON.parse(this.responseText);
      if (rp.status === 200) {
        const data = rp.data;
        const customerName = data[0].fullname;
        const orderDate = data[0].date;
        const totalAmount = data[0].total;

        let html = "";
        data.forEach((item) => {
          html += `
						<div class="order-detail-item">
				            <div>${item.item}</div>
				            <div>x${item.quantity}</div>
				            <div>${item.price} $</div>
				            <div>${Number(item.price) * Number(item.quantity)} $</div>
				        </div>
					`;
        });

        document.querySelector("#order-detail-content").innerHTML = html;

        document.querySelector(
          "#order-id-label"
        ).innerHTML = `<b>OrderID: </b>#00${orderId}`;
        document.querySelector(
          "#customer-name-label"
        ).innerHTML = `<b>Customer Name: </b>${customerName}`;
        document.querySelector(
          "#order-date-label"
        ).innerHTML = `<b>Date: </b>${orderDate}`;
        document.querySelector("#total-amount-label").innerHTML =
          totalAmount + " $";
      }
    };

    xhttp.open("POST", "php/orderdetail.php");
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + orderId);
  });
});

document
  .querySelector("#btn-close-view-order")
  .addEventListener("click", () =>
    toggleModal(event.currentTarget.closest(".modal").id)
  );
