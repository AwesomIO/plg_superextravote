function SuperExtraVote(id, vote) {
    var voteForm = document.getElementById('superextravote_' + id);

    var currentURL = window.location;
    var live_site = currentURL.protocol+'//'+currentURL.host+ev_basefolder;

    if(voteForm.className.replace(/[\n\t]/g, " ").indexOf("locked") > -1){
        if(voteForm.className.replace(/[\n\t]/g, " ").indexOf("choosable") > -1){
            SuperExtraVoteNeedAuth(id);
            return;
        }
        SuperExtraVoteAlreadyVoted(id);
        return;
    }
    var ajax=new XMLHttpRequest();
    ajax.onreadystatechange=function() {
        var response;
        if(ajax.readyState===4){
            setTimeout(function(){
                response = JSON.parse(ajax.responseText);
                switch(response.status){
                    case 'success':
                        SuperExtraVoteSuccess(id, response.data);
                        break;
                    case 'alreadyvoted':
                        SuperExtraVoteAlreadyVoted(id);
                        break;
                    case 'needauth':
                        SuperExtraVoteNeedAuth(id);
                        break;
                    default:
                        SuperExtraVoteError(id);
                        break;
                }
            },500);
        }
    };
    ajax.open("GET",live_site+"/index.php?option=com_ajax&format=raw&plugin=superextravote&rating="+vote+"&article="+id,true);
    ajax.send(null);
}
function SuperExtraVoteNeedAuth(id) {
    console.log('Нужно авторизоваться');
    var voteForm = document.getElementById('superextravote_' + id);
    if(voteForm.className.replace(/[\n\t]/g, " ").indexOf("locked")===-1){
        voteForm.className = voteForm.className + ' locked';
    }
    if(voteForm.className.replace(/[\n\t]/g, " ").indexOf("choosable")===-1){
        voteForm.className = voteForm.className + ' choosable';
    }
    var btns = voteForm.querySelectorAll('input[type="radio"]:checked');
    for(var i = 0; i < btns.length; i++){
        if(btns[i].checked){
            btns[i].checked = false;
        }
    }

    var divInfo = document.createElement('div');
    divInfo.className = "info-msg";
    divInfo.innerHTML = "Войдите, чтобы оставить свой голос.";
    voteForm.appendChild(divInfo);
    setTimeout(function() {
        divInfo.parentNode.removeChild(divInfo);
    }, 2500);
}

function SuperExtraVoteAlreadyVoted(id) {
    console.log('Уже голосовал');
    var voteForm = document.getElementById('superextravote_' + id);
    if(voteForm.className.replace(/[\n\t]/g, " ").indexOf("locked")===-1){
        voteForm.className = voteForm.className + ' locked';
    }
    var btns = voteForm.querySelectorAll('input[type="radio"]:checked');
    for(var i = 0; i < btns.length; i++){
        if(btns[i].checked){
            btns[i].checked = false;
        }
    }

    var divInfo = document.createElement('div');
    divInfo.className = "info-msg";
    divInfo.innerHTML = "Вы уже голосовали! Повторное голосование запрещено.";
    voteForm.appendChild(divInfo);
    setTimeout(function() {
        divInfo.parentNode.removeChild(divInfo);
    }, 2500);
}

function SuperExtraVoteSuccess(id, voteData) {
    console.log('Голос принят!');
    var voteForm = document.getElementById('superextravote_' + id);
    var btns = voteForm.querySelectorAll('input[type="radio"]:checked');
    for(var i = 0; i < btns.length; i++){
        if(btns[i].checked){
            btns[i].checked = false;
        }
    }
    voteForm.querySelector('.rating>span>b').innerHTML = voteData.rating;
    var divInfo = document.createElement('div');
    divInfo.className = "info-msg";
    divInfo.innerHTML = "Спасибо! Ваш голос принят.";
    voteForm.appendChild(divInfo);
    var stars = voteForm.querySelectorAll('label>.star');
    for(var s=0, i=stars.length; s < stars.length; s++, i--){
        var star=stars[s];
        if(voteData.rating >= i){
            star.className = 'star chose-full';
        }
        else if (voteData.rating === 0.5 || voteData.rating === i - 0.5){
            star.className = 'star chose-half';
        }
        else{
            star.className = 'star';
        }
    }

    setTimeout(function() {
        divInfo.parentNode.removeChild(divInfo);
    }, 2500);

}
function SuperExtraVoteError(id) {
    console.log('Ошибка');
    var voteForm = document.getElementById('superextravote_' + id);
    var btns = voteForm.querySelectorAll('input[type="radio"]:checked');
    for(var i = 0; i < btns.length; i++){
        if(btns[i].checked){
            btns[i].checked = false;
        }
    }
    var divInfo = document.createElement('div');
    divInfo.className = "info-msg";
    divInfo.innerHTML = "Ошибка! Ваш голос небыл учтён.";
    voteForm.appendChild(divInfo);
    setTimeout(function() {
        divInfo.parentNode.removeChild(divInfo);
    }, 2500);
}
