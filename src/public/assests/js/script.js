console.log('hello');

$('.component').on('click', function () {
    // console.log($this.val());
    controller = ($('#component').val());
    $.ajax({
        method: "POST",
        url: "http://localhost:8080/acl/allow",
        data: { controller: controller, 'action': 'drop' },
        // dataType: "JSON",
    }).done(function (data) {
       
        display(JSON.parse(data));
        // cart(data);
    });
});


function display(data) {
    html = " <p class='row '>\
    <label for='Actions' class='label col-2 mx-5'>Actions</label>\
    <select name='action' id='actions' class='form-control col-6 component'>";
    for(val in data) {
        html += "<option value=" + data[val] + ">" + data[val] + "</option>"
    }
    html += "</select></p >";
    document.getElementById("actions-dropdown").innerHTML = html;

}