<?php 


namespace App\Service;



class DescriptionFilter
{

    public function filter($descriptionInput) {

        $array = ['prostitution','prostitué','prostitue','pute','putes','putain','putains','tarif','tarifs','escorte','escortes','escorting','service','services','sexe','sexes','sexuel',
        'sexuelle','france','proxénète','proxénètes','proxénétisme','payant','payante','payants','payantes','vente','ventes','vendre','achat','achats','acheter','prestation','prestations',
        'tapin','débauche','débauches','tapinage','corruption','business','courtier','salope','salopes','bordel'];

        foreach ($array as $word) {
        // Works in Hebrew and any other unicode characters
        // Thanks https://medium.com/@shiba1014/regex-word-boundaries-with-unicode-207794f6e7ed
        // Thanks https://www.phpliveregex.com/
        if (preg_match('/(?<=[\s,.:;"\']|^)' . $word . '(?=[\s,.:;"\']|$)/', $descriptionInput)) return true;
        }
        return false;
    
    }

}


