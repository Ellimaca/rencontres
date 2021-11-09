window.onload = init;

function init(){

    let buttons = Array.from(document.getElementsByClassName('like_button'));
    let profil_id = document.getElementById('profil_id');

    buttons.forEach(function(item, index){
        item.addEventListener('click', function (){

            let data = {'profilId' : profil_id.value, 'isLiked' : item.value};

            fetch('ajax-likes', {method:'POST',body: JSON.stringify(data)})
                .then(function (response){
                    return response.json();
                }).then(function (data){
                    console.log(data);
                    document.getElementById('nb_likes').innerHTML = data.likes;
            })
        })
    })
}
