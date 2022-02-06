<?php

if ( $disable_invasions == "True" ) {
        header("Location: $redirect_url");
        exit();
}

?>

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col">
                            <div class="alert alert-secondary text-center" role="alert">
				<strong><?php echo i8ln("NEW INVASIONS ALARM"); ?></strong>
                            </div>
                        </div>
                    </div>

                    <form action='./actions/invasions.php' method='POST'>

			<?php include "./include/add_area_distance.php"; ?>

			<?php if (strpos($_SESSION['type'], ':user') === false) {  ?>
                        <div class="form-row align-items-center">
                            <div class="col-sm-12 my-1">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><?php echo i8ln("Role to ping"); ?></div>
                                        </div>
                                        <input type='text' id='content_add' name='content' maxlength=255 size=50 class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php } else { ?>
							<input type="hidden" id='content_add' name='content' value=''>
						<?php } ?>
			<div class="form-row align-items-center">
                            <div class="col-sm-12 my-1">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">

                                    <?php

                                        $clean_0_checked = 0;
                                        $clean_1_checked = 0;
                                        if ($all_invasion_cleaned == "1") {
                                            $clean_1_checked = 'checked';
                                        } else {
                                            $clean_0_checked = 'checked';
                                        }

                                        ?>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
					    <div class="input-group-text"><?php echo i8ln("Clean"); ?></div>
                                        </div>
                                    </div>
                                    <label class="btn btn-secondary">
                                        <input type="radio" name="clean" id="clean_0" value="clean_0"
                                            <?php echo $clean_0_checked; ?>>
					<?php echo i8ln("No"); ?>
                                    </label>
                                    <label class="btn btn-secondary">
                                        <input type="radio" name="clean" id="clean_1" value="clean_1"
                                            <?php echo $clean_1_checked; ?>>
					<?php echo i8ln("Yes"); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <?php

                        $type = explode(":", $_SESSION['type'], 2);
			if ( $type[0] == "webhook" ) { $type[0] = "discord"; }   
                        $templates_locale = @$_SESSION['templates'][$type[0]]['invasion'][$_SESSION['locale']];
                        $templates_undefined = @$_SESSION['templates'][$type[0]]['invasion']['%'];
                        $templates_list = array_merge((array)$templates_locale,(array)$templates_undefined);

                        if (count($templates_list) > 1 && $enable_templates == "True" ) {
                            echo '<div class="form-row align-items-center">
                                <div class="col-sm-12 my-1">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <div class="input-group-justify">
                                            <div class="input-group mb-1">
                                                <div class="input-group-text">Template</div>
                                            </div>
                                        </div>';
                                        foreach ( $templates_list as $key => $name ) {
                                            echo '<label class="btn btn-secondary mb-1 mr-1">';
                                            echo '<input type="radio" name="template" id="' . $name . '" value="' . $name . '">';
                                            echo $name . '</label>';
                                        }
                                    echo '</div>
                                </div>
                            </div>';
                        } ?>

                        <hr>

                        <div class='selectionList'>
                            <ul>
                                <?php

                                    global $grunts_json;
                                    $json = json_decode($grunts_json, true);
                                    $grunts=array();

				    $grunts_index_json = file_get_contents("$uicons_reward/invasion/index.json");
				    $grunts_index = json_decode($grunts_index_json, true);

				    foreach ($json as $key => $value) { 
				       $grunt_id = $key;
				       $grunt_name = $value['type'];
				       $grunt_gender = $value['gender'];

				       foreach ($grunts_index as $value) { 
					       if ( $value == $grunt_id.".png" ) { $exist = 1; break; } else { $exist = 0; }
				       }

				       if ( $exist == 1 ) {

                                    ?>

                                         <li class='text-center'><input type='checkbox' name='grunt_<?php echo $grunt_gender; ?>_<?php echo $grunt_name; ?>'
                                                 id='grunt_<?php echo $grunt_gender; ?>_<?php echo $grunt_name; ?>' />
                                             <label for='grunt_<?php echo $grunt_gender; ?>_<?php echo $grunt_name; ?>'>
                                                 <img src='<?php echo $uicons_reward; ?>/invasion/<?php echo $grunt_id; ?>.png' />
   				     	         <br><?php echo ucfirst(i8ln(strtolower($grunt_name))); ?>
                                             </label>
				        </li>
				<?php } } ?>

                            </ul>
                        </div>


                        <div class="float-right mb-3 mt-3">
			    <input class="btn btn-primary" type='submit' name='add_invasions' value='<?php echo i8ln("Submit"); ?>'>
                            <a href='<?php echo $redirect_url ?>'>
				<button type="button" class="btn btn-secondary"><?php echo i8ln("Cancel"); ?></button>
                            </a>
                        </div>

                    </form>

