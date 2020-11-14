if(document.getElementById('button')!=null){
    document.getElementById('button').addEventListener('click', function(e){
        document.getElementById('french').style.display= "none";
        document.getElementById('russian').style.display= "block";
        document.getElementById('russian').classList.add('add');
      
      
    })
}
if(document.getElementById('session').value!=null){
    document.getElementById('theme_titre').value=document.getElementById('session').value;

}