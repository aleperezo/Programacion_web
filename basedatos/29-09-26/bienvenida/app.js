
let usuarios = []; // arreglo para almacenar usuarios

const form =document.getElementById('formUsuario');
const tabla = document.getElementById('tablaUsuarios');
const inputArchivo = document.getElementById('importarJSON');
const btnDescargar = document.getElementById('descargar');

function generarID(){
return usuarios.length > 0 ? Math.max(...usuarios.map(u=>u.id))+1 : 1 ;
}

form.addEventListener('submit',(e)=>{
e.preventDefault(); //evitar recarga la pagina
const nombre = document.getElementById('nombre').value;
const correo = document.getElementById('correo').value;

// agregar nuevo usuario al array con ID generado de forma automatica

const objeto ={
id: generarID(),
nombre: nombre,
correo: correo
};

usuarios.push(objeto);
mostrarUsuarios();
form.reset();

});

function mostrarUsuarios(){
tabla.innerHTML='';
// por cada usuario en el array se crea una fila
usuarios.forEach((user,index)=>{
const row = document.createElement('tr');

row.innerHTML=`
<td>${user.id}</td>
<td>${user.nombre}</td>
<td>${user.correo}</td>
<td><button onclick="eliminarUsuario(${index})">Eliminar</button> </td>
`;
tabla.appendChild(row);

});
}
// let usuarios = []; //arreglo para almacenar los usuarios
// //let es un tipo de variable que no se puede reasignar, pero si se puede modificar su contenido.

// const form = document.getElementById("formUsuario"); //obtenemos el formulario del HTML
// const tabla = document.getElementById("tablaUsuarios"); //obtenemos la tabla del HTML
// const inputArchivo = document.getElementById("importarJSON"); //obtenemos el input del archivo del HTML
// const btnDescargar = document.getElementById("descargar"); //obtenemos el boton de descargar del HTML

// function generarID(){ //Variable local para generar un ID unico para cada usuario
//     return usuarios.length > 0 ? Math.max(...usuarios.map(u=>u.id)) + 1 : 1; 
//     //si es verdadero,se sumara 1 al ID maximo, si es falso, se asignara 1 como ID
// }

// form.addEventListener("submit"), (e)=>{
//     e.preventDefault(); //evitamos que se recargue la pagina al enviar el formulario
//     const nombre = document.getElementById("nombre").value; //obtenemos el valor del input nombre
//     const correo = document.getElementById("correo").value; //El input es un elemento de formulario que permite al usuario ingresar datos.
//     //En este caso, el input es de tipo texto y se utiliza para capturar el correo electrónico del usuario.
//     //agregar usuario al array con ID generado

//     const objeto ={
//         id: generarID(),
//         nombre: nombre,
//         correo: correo
//     }
//     usuarios.push(objeto); //agregamos el objeto al arreglo de usuarios
//     mostrarUsuarios(); //pintar los datos correspondientemente en la tabla
//     form.reset(); //limpiamos el formulario

//  };
//  //vamos a escuhar un evento en el formulario, cuando se envie el formulario se ejecutara la funcion

// function mostrarUsuarios(){ //funcion para mostrar los usuarios en la tabla
//     tabla.innerHTML='';
//     //Por cada usuario en el array (arreglo) se crea una fila
//     usuarios.forEach((user,index)=>{
//         const row = document.createElement('tr');

//         row.innerHTML = `
//             <id>${user.id}</td>
//             <td>${user.nombre}</td>
//             <td>${user.correo}</td>
//             <td><button onclick="eliminarUsuario(${index})">Eliminar</button></td>
//         `;
//         tabla.appendChild(row);


//     });
// }

 //Base de datos no relacional, JS es un ejemplo de ella, ya que no tiene una estructura fija y permite 
 //almacenar datos de manera flexible. En este caso, estamos utilizando un arreglo para almacenar los usuarios, 
 //lo que nos permite agregar, eliminar y modificar usuarios de manera sencilla.