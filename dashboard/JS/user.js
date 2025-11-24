const addUserFrm = document.querySelector("#add-user-frm");
const deleteUserBtn = document.querySelectorAll(".btn-delete");
const editUserBtn = document.querySelectorAll(".btn-edit");

addUserFrm.addEventListener("submit", function (e) {
  e.preventDefault();

  const firstname = this.querySelector("input[name='fname']").value.trim();
  const lastname = this.querySelector("input[name='lname']").value.trim();
  const email = this.querySelector("input[name='email']").value.trim();
  const password = this.querySelector("input[name='password']").value.trim();
  const cfPassword = this.querySelector(
    "input[name='cfpassword']"
  ).value.trim();
  const role = this.querySelector("select").value.trim();

  if (firstname == "" || email == "" || password == "" || cfPassword == "") {
    return alert("All Fields are required.");
  }

  if (firstname.search(/^[a-zA-Z]+$/) == -1) {
    return alert("Firstname must be string.");
  }
  if (lastname != "" && lastname.search(/^[a-zA-Z]+$/) == -1) {
    return alert("Lastname must be string.");
  }

  if (
    email.search(
      /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/
    ) == -1
  ) {
    return alert("Invalid Email Address.");
  }

  if (password.length < 8) {
    return alert("Password must be at least 8 characters long.");
  }

  if (cfPassword != password) {
    return alert("Passwords do not match.");
  }

  let formdata = new FormData();

  formdata.append("fname", firstname);
  formdata.append("lname", lastname);
  formdata.append("email", email);
  formdata.append("password", password);
  formdata.append("role", role);

  const xhttp = new XMLHttpRequest();

  xhttp.onload = function () {
    const rp = JSON.parse(this.responseText);
    alert(rp.message);
    if (rp.status == 200) {
      location.reload();
    }
  };

  xhttp.open("POST", "./PHP/user.php");
  xhttp.send(formdata);
});

deleteUserBtn.forEach((delBtn) => {
  // delete user
  delBtn.addEventListener("click", (evt) => {
    const userId = evt.target.attributes["data-id"].value.trim();
    if (confirm("Are you sure to delete this user?")) {
      // mean user want to delete this user

      let formdata = new FormData();
      formdata.append("deleteId", userId);
      const xhttp = new XMLHttpRequest();
      xhttp.onload = function () {
        console.log(this.responseText);

        const rp = JSON.parse(this.responseText);
        alert(rp.message);
        if (rp.status == 200) {
          location.reload();
        }
      };
      xhttp.open("POST", "./PHP/user.php");
      xhttp.send(formdata);
    }
  });
});

editUserBtn.forEach((editBtn) => {
  editBtn.addEventListener("click", (evt) => {
    const data = JSON.parse(evt.target.attributes["data-user"].value);
    const editFrm = document.querySelector("#edit-user-frm");
    const tempArr = data.name.split(" ");
    editFrm.querySelector("input[name=fname]").value = tempArr[0];
    editFrm.querySelector("input[name=lname]").value = tempArr[1];
    editFrm.querySelector("input[name=email]").value = data.email;
    editFrm.querySelector("input[name=user-id]").value = data.id;
    editFrm.querySelector("select").value = data.role;
  });
});

document.querySelector("#edit-user-frm").addEventListener("submit", (e) => {
  e.preventDefault();
  const editFrm = e.target;

  const firstname = editFrm.querySelector("input[name=fname]").value.trim();
  const lastname = editFrm.querySelector("input[name=lname]").value.trim();
  const email = editFrm.querySelector("input[name=email]").value.trim();
  const role = editFrm.querySelector("select").value.trim();
  const userId = editFrm.querySelector("input[name=user-id]").value.trim();

  const isChangePassword = editFrm.querySelector(
    "input#chk-change-password"
  ).checked;
  const password = editFrm
    .querySelector("input[name=new-password]")
    .value.trim();

  if (firstname == "" || email == "" || role == "") {
    return alert("All Fields are required.");
  }

  if (firstname.search(/^[a-zA-Z]+$/) == -1) {
    return alert("Firstname must be string.");
  }

  if (lastname != "" && lastname.search(/^[a-zA-Z]+$/) == -1) {
    return alert("Lastname must be string.");
  }

  if (
    email.search(
      /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/
    ) == -1
  ) {
    return alert("Invalid Email Address.");
  }

  if (isChangePassword) {
    if (password.length < 8) {
      return alert("Password must be at least 8 characters long.");
    }
  }

  let formdata = new FormData();
  formdata.append("userId", userId);
  formdata.append("firstname", firstname);
  formdata.append("lastname", lastname != "" ? lastname : "");
  formdata.append("email", email);
  formdata.append("role", role);
  formdata.append("isChangePassword", isChangePassword);
  if (isChangePassword) {
    formdata.append("password", password);
  }
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function () {
    console.log(this.responseText);

    const rp = JSON.parse(this.responseText);
    alert(rp.message);
    if (rp.status === 200) {
      location.reload();
    }
  };

  xhttp.open("POST", "./PHP/user.php");
  xhttp.send(formdata);
});

document
  .querySelector("#chk-change-password")
  .addEventListener("click", (evt) => {
    const editFrm = document.querySelector("#edit-user-frm");
    if (evt.target.checked) {
      editFrm
        .querySelector("input[name=new-password]")
        .removeAttribute("disabled");
    } else {
      editFrm
        .querySelector("input[name=new-password]")
        .setAttribute("disabled", "");
    }
  });
