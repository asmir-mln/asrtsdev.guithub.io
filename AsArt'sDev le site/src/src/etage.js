import React,{useEffect,useState} from "react"

function Etage({categorie}){

const [livres,setLivres] = useState([])

useEffect(()=>{

fetch("http://localhost:5000/api/livres/"+categorie)

.then(res=>res.json())

.then(data=>setLivres(data))

},[])

return(

<div className="etage">

<h2>Etage : {categorie}</h2>

{livres.map(livre=>(
<div key={livre.id}>
{livre.titre}
</div>
))}

</div>

)

}

export default Etage