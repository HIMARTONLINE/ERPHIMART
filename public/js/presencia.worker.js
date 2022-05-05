this.addEventListener('message', function(e) {
    var xhttp = new XMLHttpRequest();

    var param = [];
    for(key in e.data) {
    	param.push(key+'='+e.data[key]);
    }

    xhttp.open('POST', '/setpresencia', true);
    
    xhttp.setRequestHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8');
    xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4) {
            if(xhttp.status === 200) {
                self.postMessage(xhttp.responseText);
            } else {
                self.postMessage({error : true});
            }
        }
    }
    
    xhttp.send(param.join('&'));
}, false);