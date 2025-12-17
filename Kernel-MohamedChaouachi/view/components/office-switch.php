<?php
/**
 * Composant de switch entre Front Office et Back Office
 * Usage: include_once(__DIR__ . '/../components/office-switch.php');
 *        echo renderOfficeSwitch('front', 'projet', $id);
 * 
 * @param string $currentOffice 'front' ou 'back'
 * @param string $section 'projet' ou 'actualite'
 * @param int|null $id ID de l'élément (optionnel)
 */
function renderOfficeSwitch($currentOffice = 'front', $section = 'projet', $id = null) {
    $isFront = ($currentOffice === 'front');
    
    // Déterminer les URLs de destination
    if ($section === 'projet') {
        if ($id) {
            $frontUrl = "../../FrontOffice/detailsprojet.php?id=" . $id;
            $backUrl = "../../BackOffice/projet/listeProjet.php";
        } else {
            $frontUrl = $isFront ? "#" : "../../FrontOffice/listeprojet.php";
            $backUrl = $isFront ? "../../BackOffice/projet/listeProjet.php" : "#";
        }
    } else { // actualite
        $frontUrl = $isFront ? "#" : "../../FrontOffice/listeActualite.php";
        $backUrl = $isFront ? "../../BackOffice/actualite/listeActualite.php" : "#";
    }
    
    ob_start();
    ?>
    <style>
        .office-switch-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: white;
            padding: 8px 12px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .office-switch-label {
            font-size: 12px;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
        }
        
        .office-switch {
            position: relative;
            display: inline-block;
            width: 120px;
            height: 36px;
        }
        
        .office-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .office-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            transition: .4s;
            border-radius: 34px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 8px;
        }
        
        .office-slider:before {
            position: absolute;
            content: "";
            height: 28px;
            width: 56px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 34px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        input:checked + .office-slider:before {
            transform: translateX(60px);
        }
        
        .office-text {
            font-size: 11px;
            font-weight: 700;
            color: white;
            z-index: 1;
            pointer-events: none;
            text-transform: uppercase;
        }
        
        .office-text.front {
            margin-left: 2px;
        }
        
        .office-text.back {
            margin-right: 2px;
        }
    </style>
    
    <div class="office-switch-container">
        <span class="office-switch-label">
            <i class="bi bi-arrow-left-right"></i>
        </span>
        <label class="office-switch">
            <input type="checkbox" <?php echo $isFront ? '' : 'checked'; ?> 
                   onchange="window.location.href='<?php echo $isFront ? $backUrl : $frontUrl; ?>'">
            <span class="office-slider">
                <span class="office-text front">Front</span>
                <span class="office-text back">Back</span>
            </span>
        </label>
    </div>
    <?php
    return ob_get_clean();
}
?>
