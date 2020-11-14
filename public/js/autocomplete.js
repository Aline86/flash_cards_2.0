document.getElementById('button').addEventListener('click', function(e){
    document.getElementById('french').style.display= "none";
    document.getElementById('russian').style.display= "block";
    document.getElementById('russian').classList.add('add');
  
  
})
console.log(document.getElementById('session'))
document.querySelector('select').value=document.getElementById('session').value;

