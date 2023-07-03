
$(document).ready(function () {
    $('#shorten').submit(function (e) {
        e.preventDefault();
        $.post('/user/shorten', $(this).serialize(), function (res) {
            document.location.href = '/success?link=' + res.shortUrl;
        });
    });
});

//{#если текущий пользователь user#}
$(document).ready(function() {
var xhr = new XMLHttpRequest();

var url = "http://127.0.0.1:8000/api/current-user/links";

xhr.open("GET", url, true);

xhr.onload = function () {
  
  if (xhr.status === 200) {
    
    var data = JSON.parse(xhr.responseText).links;
    
    var html = $('#link-list');
    for (var i = 0; i < data.length; i++) {
      var row = '<ul>' +
     '<li>'+'LongUrl ' + '<a href="">'+ data[i].longUrl  + '</a>' + '</li>' + 
      '<li>' + 'ClickCount ' + data[i].clickCount + '</li>' +
                '</ul>';
                html.append(row);  
    }
  }
}
xhr.send();
});

//{#если текущий пользователь admin#}
$(document).ready(function() {
    var xhr = new XMLHttpRequest();
    
    var url = "http://127.0.0.1:8000/admin/url";
    
    xhr.open("GET", url, true);
    
    xhr.onload = function () {
      
      if (xhr.status === 200) {
        
        var data = JSON.parse(xhr.responseText).links;
        console.log(xhr.responseText);
        var html = $('#admin-link-list');
        for (var i = 0; i < data.length; i++) {
          var row = '<ul>' +
         '<li>'+'LongUrl ' + '<a href="/go-{shortCode}">'+ data[i].longUrl  + '</a>' + '</li>' + 
          '<li>' + 'ClickCount ' + data[i].clickCount + '</li>' +
                    '</ul>';
                    html.append(row);  
        }
      }
    }
    xhr.send();
    });