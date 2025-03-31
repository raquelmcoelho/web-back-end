CREATE OR REPLACE FUNCTION inscrire_client(
    p_nom VARCHAR(50), 
    p_prenom VARCHAR(50), 
    p_adresse VARCHAR(255), 
    p_code_postal VARCHAR(10), 
    p_ville VARCHAR(100), 
    p_pays VARCHAR(50)
) RETURNS INT AS $$
DECLARE
    v_id INT;
BEGIN
    SELECT code_client INTO v_id 
    FROM clients 
    WHERE nom = p_nom AND prenom = p_prenom AND adresse = p_adresse;
    
    IF v_id IS NOT NULL THEN
        RETURN 0;
    END IF;

    INSERT INTO clients (nom, prenom, adresse, code_postal, ville, pays)
    VALUES (p_nom, p_prenom, p_adresse, p_code_postal, p_ville, p_pays) 
    RETURNING code_client INTO v_id;

    RETURN v_id;
END;
$$ LANGUAGE plpgsql;

