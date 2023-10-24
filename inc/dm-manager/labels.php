<?php

function dm_manager_building_name($val, $en=false) {
    if ($val == 'A') return $en ? 'Building A' : 'Edificio A';
    if ($val == 'B') return $en ? 'Building B' : 'Edificio B';
    if ($val == 'X') return 'ex DMA';
    return $val;
  }

function format_person_name($ob) {
    return $ob['firstName'] . " " . $ob['lastName'] . " (" . implode(', ', array_map(function ($a) {return $a['name'];},$ob['affiliations'])) . ")";
} 

function dm_manager_get_role_label($role, $en, $genre = 'm') {
	$ret = $role;

	$roles = [
		'PO' => ['Professore Ordinario', 'Professoressa Ordinaria', 'Full Professor', 'Full Professor'],
		'PA' => ['Professore Associato', 'Professoressa Associata', 'Associate Professor', 'Associate Professor'],
		'RTDb' => ['Ricercatore a tempo determinato senior', 'Ricercatrice a tempo determinato senior', 'Tenure-track Assistant Professor', 'Tenure-track Assistant Professor'],
		'RTDa' => ['Ricercatore a tempo determinato junior', 'Ricercatrice a tempo determinato junior', 'Non-Tenure-Track Assistant Professor', 'Non-Tenure-Track Assistant Professor'],
		'RIC' => ['Ricercatore a tempo indeterminato', 'Ricercatrice a tempo indeterminato', 'Tenured Assistant Professor', 'Tenured Assistant Professor'],
		'Assegnista' => ['Assegnista', 'Assegnista', 'Postdoctoral Fellow', 'Postdoctoral Fellow'],
		'Dottorando' => ['Dottorando', 'Dottoranda', 'Ph.D. Student', 'Ph.D. Student'],
		'PTA' => ['Personale Tecnico Amministrativo', 'Personale Tecnico Amministrativo', 'Administrative Staff', 'Administrative Staff'],
		'Professore Emerito' => ['Professore Emerito', 'Professore Emerito', 'Emeritus Professor', 'Emeritus Professor'],
		'Collaboratore' => ['Collaboratore', 'Collaboratrice', 'Affiliate Member', 'Affiliate Member'], 
		'Docente Esterno' => ['Docente con contratto esterno', 'Docente con contratto esterno', 'Adjunct Professor', 'Adjunct Professor'],
		'Studente' => ['Studente', 'Studentessa', 'Student', 'Student'],
	];

	if (isset($roles[$role])) {
		$i = 0;

		if ($genre == 'f') {
			$i++;
		}

		if ($en) {
			$i += 2;
		}

		$ret = $roles[$role][$i];
	}

	return $ret;
}

function dm_manager_get_research_group_label($SSD, $en) {
    switch ($SSD) {
        case 'MAT/01':
          $research_group = $en ? 'Mathematical Logic' : 'Logica Matematica';
          break;
        case 'MAT/02':
          $research_group = 'Algebra';
          break;
        case 'MAT/03':
          $research_group = $en ? 'Geometry' : 'Geometria';
          break;
        case 'MAT/04':
          $research_group = $en ? 'Mathematics Education and History of Mathematics' : 'Didattica della Matematica e Storia della Matematica';
          break;
        case 'MAT/05':
          $research_group = $en ? 'Mathematical Analysis' : 'Analisi Matematica';
          break;
        case 'MAT/06':
          $research_group = $en ? 'Probability and Mathematical Statistics' : 'Probabilità e Statistica Matematica';
          break;
        case 'MAT/07':
          $research_group = $en ? 'Mathematical Physics' : 'Fisica Matematica';
          break;
        case 'MAT/08':
          $research_group = $en ? 'Numerical Analysis' : 'Analisi Numerica';
          break;
        default:
          $research_group = $SSD;
          break;
    }

    return $research_group;
}

function dm_manager_floor_label($floor, $en) {
    switch ($floor) {
        case 0:
          $floor_desc = $en ? 'Ground floor' : 'Piano terra';
          break;
        case 1:
          $floor_desc = $en ? 'First floor' : 'Primo piano';
          break;
        case 2:
          $floor_desc = $en ? 'Second floor' : 'Secondo piano';
          break;
        case 3:
          $floor_desc = $en ? 'Third floor' : 'Terzo piano';
          break;
        default:
          $floor_desc = "";
    }

    return $floor_desc;
}
?>