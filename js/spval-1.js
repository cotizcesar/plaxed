/*
 * Nombre: supervalidacion
 * Version: 1.1
 * Autor: Jesús R Cabrera S / JrcsDev
 * Email: jrcsdev@gmail.com
 * Fecha de Actualización: 13/04/2012
 * 
*/
function supervalidacion(f)
{	resp=true;
	//self=this;
	
	//Funcion Trim
	String.prototype.Trim=function()
	{	cadena=this;
		cadena=cadena.replace(/^\s+/,'').replace(/\s+$/,'');
		  return(cadena);
	}
	//Quita todos los espacios en blanco
	String.prototype.Limpiar=function()
	{	cadena=this;
		cadena=cadena.replace(/\s+/g,'')
		  return(cadena);
	}
	//Simulamos el indexOf en caso de usar IE
	if (!Array.indexOf)
	{	Array.prototype.indexOf = function(obj)
		{	for(var i=0; i<this.length; i++)
			{	if (this[i]==obj)
				{	return i;
				}
			}
		return -1;
		}
	}
	//Verifica si un campo esta vacio
	String.prototype.esVacio=function()
	{	frase=this;
		patron=/^\s*$/;
		if (patron.test(frase))
			return true;
		else
			return false;
	}
	//Verifica si es un rango cerrado
	String.prototype.esRangoCerrado=function()
	{	frase=this;
		patron=/^\[((\d+(\.\d+)?)?,\d+(\.\d+)?|\d+(\.\d+)?,(\d+(\.\d+)?)?)\]$/;
		
		if (patron.test(frase))
			return true;
		else
			return false;
	}
	//Verifica si es un rango abierto
	String.prototype.esRangoAbierto=function()
	{	frase=this;
		patron=/^\(((\d+(\.\d+)?)?,\d+(\.\d+)?|\d+(\.\d+)?,(\d+(\.\d+)?)?)\)$/;
		if (patron.test(frase))
			return true;
		else
			return false;
	}
	//Quita todos los simbolos
	String.prototype.BorrarSimbolos=function()
	{	cadena=this;
		cadena=cadena.replace(/\[+/g,'').replace(/\]+/g,'').replace(/\(+/g,'').replace(/\)+/g,'');
		  return(cadena);
	}
	//Verifica si una cadena se puede interpretar como un entero
	String.prototype.esEntero=function()
	{	frase=this;
		patron=/^[-+]?\d+$/i;
		if (patron.test(frase))
			return true;
		else
			return false;
	}
	//Verifica si una cadena se puede interpretar como un real
	String.prototype.esReal=function()
	{	frase=this;
		patron=/^[-+]?\d+(\.\d+)?$/i;
		if (patron.test(frase))
			return true;
		else
			return false;
	}
	//Verifica si una cadena se puede interpretar como un correo valido
	String.prototype.CorreoValido=function()
	{	frase=this;
		patron=/^[0-9a-z_\-\.]+@[0-9a-z\-\.]+\.[a-z]{2,4}$/i;
		if (patron.test(frase))
			return true;
		else
			return false;
	}
	//Verifica si una cadena se puede interpretar como una fecha dd/mm/aaaa
	String.prototype.esFecha=function()
	{	frase=this;
		patron=/^(3[01]|0?[1-9]|[12]\d)(\/)(0?[1-9]|1[012])(\/)\d{4}$/;
		if (patron.test(frase))
			return true;
		else
			return false;
	}
	//Regla para validar select
	String.prototype.esReglaSelect=function()
	{	frase=this;
		patron=/^\![\w|-]+$/i;
		if (patron.test(frase))
			return true;
		else
			return false;
	}
	//Regla para campos iguales
	String.prototype.esRegEq=function()
	{	frase=this;
		patron=/^eq\(.+\)$/i;
		if (patron.test(frase))
			return true;
		else
			return false;
	}
	//Regla para valor especifico, como para validar captcha
	String.prototype.esValor=function()
	{	frase=this;
		patron=/^val\(.+\)$/i;
		if (patron.test(frase))
			return true;
		else
			return false;
	}
	//Busca un elemento en un arreglo
	function enArreglo(val,arr)
	{	if (arr.indexOf(val)>-1)
			return true;
		else
			return false;
	}
	//Colocar la expresion regular en el parametro. Ej: spval="eval(EXPRESION_REGULAR)"
	function evaluaExpresion(expresion, valor){
		expresion=expresion.replace(/\\/g, '\\');
		var re = new RegExp(eval(expresion));
		if (valor.match(re)){
			return true;
		}
		else{
			return false;
		}
	}
	//Verificar si es una expresion regular literal
	String.prototype.esExpresionRegular=function()
	{	frase=this;
		patron=/^eval\(/i;
		if (patron.test(frase))
			return true;
		else
			return false;
	}
	
	var nradio="";
	var vradio=false;
	
	for (i=0;i<f.elements.length;i++)
	{	
		campo=f.elements[i];
		
		if (campo.type=="submit" || campo.type=="button") continue;
		
		if (campo.name.esVacio())
		{	alert('Debe asignar la propiedad name a todos los campos del formulario');
			return false;
		}		
		
		if (campo.name==nradio)
			continue;		
		nradio="";
		vradio=false;	
		
		if (campo.type=="radio")
		{	nradio=campo.name;
			rd=document.getElementsByName(campo.name);
			for (x=0;x<rd.length;x++)
			{	if (rd[x].checked==true) vradio=true;					
			}
			if (!vradio)
			{	error=true;
				if (msjError!="")
					alert(msjError);
				else
					alert('Debe seleccionar un valor');
				rd[0].focus();
				return false;
			}	
			continue; //esto es para que salte el chequeo del parametro "regla" que no existe para los radio
		}
		else
		{	var dato=campo.value;
			var regla=f.elements[i].getAttribute('spval');
			var msjError=f.elements[i].getAttribute('msjError');

			if (regla==null)
				continue;
			regla=regla.Trim();
			if (regla.esVacio())
				continue; // no tiene regla
			//
			if (msjError==null)
				msjError="";
			msjError=msjError.Trim();

			//
			var regla=regla.toLowerCase();
			regla=regla.Limpiar();
			if (regla.esExpresionRegular()){
				var reglas = new Array();
				reglas[0]=regla;
			}
			else{
				var reglas=regla.split("|");						
			}
				
			
			var requerido=false;
			var entero=false;
			var real=false;
			
			var abierto_i=false;
			var abierto_f=false;
			var cerrado_i=false;
			var cerrado_f=false;
			var correo=false;
			var fecha=false;
			var expresion_regular=false;
			var expresion_regular_txt="";
			var valor_especifico=false;
			var valor_especifico_txt="";
			var eq=false;
			var eq_campo="";
			var select_car="";
			
			var min_a="";
			var max_a="";
			var min_c="";
			var max_c="";
			var rango_abierto="";
			var rango_cerrado="";
			
			for (j=0;j<reglas.length;j++)
			{	
					
				if (reglas[j]=="*") requerido=true;
				if (reglas[j]=="int") entero=true;
				if (reglas[j]=="real") real=true;
				if (reglas[j]=="@") correo=true;
				if (reglas[j]=="#") fecha=true;
				
				if (reglas[j].esReglaSelect())
						select_car=reglas[j].substr(1);
				if (reglas[j].esValor()){
					valor_especifico=true;					
					valor_especifico_txt=reglas[j].substr(4,reglas[j].length-5);					
				}
				if (reglas[j].esExpresionRegular()){
					expresion_regular=true;					
					expresion_regular_txt=reglas[j].substr(5,reglas[j].length-6);					
				}

				if (reglas[j].esRangoAbierto())
				{	tmp=reglas[j].BorrarSimbolos();
					tmp1=tmp.split(",");					
					if (!tmp1[0].esVacio())
					{	min_a=tmp1[0];
						abierto_i=true;
					}
					if (!tmp1[1].esVacio())
					{	max_a=tmp1[1];
						abierto_f=true;
					}						
				}
				if (reglas[j].esRangoCerrado())
				{	tmp=reglas[j].BorrarSimbolos();
					tmp1=tmp.split(",");
					if (!tmp1[0].esVacio())
					{	min_c=tmp1[0];
						cerrado_i=true;
					}
					if (!tmp1[1].esVacio())
					{	max_c=tmp1[1];
						cerrado_f=true;
					}
				}
				if (reglas[j].esRegEq())
				{	
					str1=reglas[j].split("(");
					str2=str1[1].split(")");
					eq_campo=str2[0];
					eq=true;
				}
			}		
			
			if (campo.type=="select-one")
			{	
				if (!select_car.esVacio() && dato==select_car)
				{	if (msjError!="")
						alert(msjError);
					else
						alert('Debe seleccionar un elemento');
					campo.focus();
					return false;
				}
			}
			if (campo.type=="text" || campo.type=="textarea" || campo.type=="password")
			{	
				if (requerido && dato.esVacio())
				{	if (msjError!="")
						alert(msjError);
					else
						alert('El campo es requerido');
					campo.focus();
					return false;
				}
				
				if (entero && !dato.esEntero() && !dato.esVacio())
				{	if (msjError!="")
						alert(msjError);
					else
						alert('El campo requiere un valor entero');
					campo.focus();
					return false;
				}
				if (real && !dato.esReal() && !dato.esVacio())
				{	if (msjError!="")
						alert(msjError);
					else
						alert('El campo requiere un valor real');
					campo.focus();
					return false;
				}
				if (correo && !dato.CorreoValido() && !dato.esVacio())
				{	if (msjError!="")
						alert(msjError);
					else
						alert('El formato de correo es inválido');
					campo.focus();
					return false;
				}
				if (fecha && !dato.esFecha() && !dato.esVacio())
				{	if (msjError!="")
						alert(msjError);
					else
						alert('El formato de fecha es inválido');
					campo.focus();
					return false;
				}
				if (valor_especifico && dato!=valor_especifico_txt){					
					if (msjError!="")
						alert(msjError);
					else
						alert('El dato introducido es incorrecto');
					campo.focus();
					return false;
				}
				if (expresion_regular && !evaluaExpresion(expresion_regular_txt, dato)){
					if (msjError!="")
						alert(msjError);
					else
						alert('Los datos no cumplen con el formato');
					campo.focus();
					return false;
				}

				//Esta linea es equivalente a la que le sigue, pero eval() es algo inseguro jeje
				//if (eq && dato!=eval('f.'+eq_campo+'.value')) 
				if (eq && dato!=f[eq_campo].value)
				{	if (msjError!="")
						alert(msjError);
					else
						alert('Los campos no coinciden');
					campo.focus();
					return false;
				}				
				
				if (abierto_i || abierto_f)
				{	txt=(abierto_i && abierto_f)? "El número debe ser >= "+min_a+" y <= "+max_a:"";
					if (txt.esVacio())
					{	txt=(abierto_i)?"El número debe ser > "+min_a:"El número debe ser < "+max_a;
					}
					if ((parseFloat(dato,10)<=parseFloat(min_a,10)) || (parseFloat(dato,10)>=parseFloat(max_a,10)))
					{	if (msjError!="")
							alert(msjError);
						else
							alert(txt)
						campo.focus();
						return false;
					}					
				}
				if (cerrado_i || cerrado_f)
				{	txt=(cerrado_i && cerrado_f)? "El número debe >= "+min_c+" y <= "+max_c:"";
					if (txt.esVacio())
					{	txt=(cerrado_i)?"El número debe ser > "+min_c:"El número debe ser < "+max_c;
					}
					if ((parseFloat(dato,10)<parseFloat(min_c,10)) || (parseFloat(dato,10)>parseFloat(max_c,10)))
					{	if (msjError!="")
							alert(msjError);
						else
							alert(txt)
						campo.focus();
						return false;
					}					
				}
			}
		}
	}
	return true;
}
