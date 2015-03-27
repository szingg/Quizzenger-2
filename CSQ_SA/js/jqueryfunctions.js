$(function(){

	bootbox.setDefaults({
		locale: "de",
	});

	window.setTimeout(function() {
		$(".alertautoremove").fadeTo(1500, 0).slideUp(500, function(){
			$(this).remove();
		});
	}, 3000);

	$('#quiz_generator_form_count').on('change', function() {
		$('#quiz_generator_form_count_text').val($(this).val());
	});
	$('#quiz_generator_form_count').on('input', function() {
		$('#quiz_generator_form_count_text').val($(this).val());
	});

	$('.remove-row').on('click', function(event) {
		var element = $(this);
		var id = element.data('qid');
		var type = element.data('type');
		var message = 'Sind Sie sicher?';
		switch(type) {
			case 'question':
				message = 'Wollen Sie diese Frage wirklich löschen?';
				break;
			case 'quiz':
				message = 'Wollen Sie dieses Quiz wirklich löschen?';
				break;
			case 'questionreports':
				message = 'Wollen Sie diesen Eintrag wirklich als erledigt markieren?';
				break;
			case 'user':
				message = 'Wollen Sie diesen Benutzer wirklich sperren?';
				break;
			case 'ratingreports':
				message = 'Wollen Sie diesen Eintrag wirklich als erledigt markieren?';
				break;
			case 'subcat':
				message = 'Wollen Sie diese Kategorie und deren Fragen wirklich löschen?';
				break;
			case 'userreports':
				message = 'Wollen Sie die Meldungen für diesen User wirklich entfernen?';
				break;
			default:
				break;
		}

		bootbox.confirm( message, function(result) {
			if(result){
				if(type == 'question'){
					deleteQuestion(id);
				} else if(type == 'quiz'){
					deleteQuiz(id);
				} else if(type == 'questionreports'){
					deleteReports(id, 'question')
				} else if(type == 'ratingreports'){
					deleteReports(id, 'rating')
				} else if(type == 'user'){
					inactiveUser(id);
				} else if(type == 'subcat'){
					removeSubCat(id);
				} else if(type == 'userreports'){
					deleteReports(id,'user')
				}
				element.closest('tr')
				.children('td')
				.animate({ padding: 0 })
				.wrapInner('<div />')
				.children()
				.slideUp(function() { $(this).closest('tr').remove(); });
			}
		});
	});


	// ================================================================================
	// DATATABLES
	// ================================================================================
	$('#tableQuizPerformances').DataTable( {
		responsive: true
	} );

	$('#tableSubCats').DataTable( {
		responsive: true,
		"aLengthMenu": [[5, 10, 15, -1], [5,10,15, "Alle"]]
	} );

	$('#tableQuestionList').DataTable( {
		responsive: true,
		"order": [[ 1, "desc" ]]
		// aoColumnDefs: [
		// {
		// bSortable: false,
		// aTargets: [ -1 ]
		// }
		// ]
	} );
	$('#tableQuizList').DataTable( {
		responsive: true
	} );
	$('#tableQuestionPerformances').DataTable( {
		responsive: true
	});
	$('#tableReportedContents').DataTable( {
		responsive: true,
		"order": [[ 2, "desc" ]],
		"bFilter": false,
		"paging":   false,
	});
	$('#tableModerationContents').DataTable({
		responsive: true,
		"order": [[ 3, "desc" ]],
		"bFilter": false,
		"paging":   false,
	});
	$('#tableNewGame').DataTable({
		responsive: true
	});
	$('#tableOpenGames').DataTable({
		responsive: true
	});
	$('#tableReportUserList').DataTable({
		"responsive" : true,
		"autoWidth" : false,
		"columnDefs" : [
			{"className" : "dt-right", "targets": [0, 4, 5]},
			{"width" : "45%", "targets" : [1]},
			{"width" : "20%", "targets" : [2, 3]},
			{"width" : "5%", "targets" : [0, 4, 5]}
		],
	});
	$('#tableReportQuestionList').DataTable({
		"responsive" : true,
		"autoWidth" : false,
		"columnDefs" : [
			{"className" : "dt-right", "targets": [0, 5, 6, 7]},
			{"width" : "5%", "targets" : [5, 6, 7]},
			{"width" : "10%", "targets" : [4]}
		],
	});
    $('#tableOpenGames').DataTable( {
        "responsive": true
    } );
});
// ================================================================================
// VALIDATORS
// ================================================================================


$(document).ready(function() {
	$('.opquestion_form').bootstrapValidator({
		message: 'This value is not valid',
		fields: {
			opquestion_form_chosenCorrectAnswer: {
				validators: {
					notEmpty: {
						message: 'Es muss eine Antwort als korrekt markiert werden'
					},
					between: {
						min: 1,
						max: 4,
						message: 'Dies ist keine gültige Antwort (1-4)'
					}
				}
			},
			opquestion_form_chosenCategoryName:{
				validators:{
					notEmpty: {
						message: ' '
					}
				}
			},
			opquestion_form_chosenCategory:{
				validators:{
					notEmpty: {
						message: 'Es muss eine Kategorie gewählt werden'
					},
					between: {
						min: -1,
						max: 999,
						message: 'Dies ist keine gültige Kategorie'
					}
				}
			},
			opquestion_form_questionText: {
				validators: {
					notEmpty: {
						message: ' '
					}
				}
			},
			opquestion_form_answer1: {
				validators: {
					notEmpty: {
						message: ' '
					}
				}
			},
			opquestion_form_answer1: {
				validators: {
					notEmpty: {
						message: ' '
					}
				}
			},
			opquestion_form_answer2: {
				validators: {
					notEmpty: {
						message: ' '
					}
				}
			},
			opquestion_form_answer3: {
				validators: {
					notEmpty: {
						message: ' '
					}
				}
			},
			opquestion_form_answer4: {
				validators: {
					notEmpty: {
						message: ' '
					}
				}
			}
		}
	});
});




$(document).ready(function() {
	$('.change_password_form').bootstrapValidator({
		message: 'This value is not valid',
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			change_password_form_password: {
				validators: {
					notEmpty: {
						message: 'Das Passwort darf nicht leer sein'
					},
					identical: {
						field: 'change_password_form_password_confirm',
						message: 'Die Passwörter stimmen nicht überein'
					},
					stringLength: {
						min: 6,
						message: 'Das Passwort muss mindestens 6 Zeichen enthalten'
					}
				}
			},
			change_password_form_password_confirm: {
				validators: {
					notEmpty: {
						message: 'Das Passwort darf nicht leer sein'
					},
					identical: {
						field: 'change_password_form_password',
						message: ' '
					},
					stringLength: {
						min: 6,
						message: 'Das Passwort muss mindestens 6 Zeichen enthalten'
					}
				}
			 }
		}
	});
});


$(document).ready(function() {
	$('.login_form').bootstrapValidator({
		message: 'This value is not valid',
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			login_form_password: {
				validators: {
					stringLength: {
						min: 6,
						message: 'Das Passwort muss mindestens 6 Zeichen lang sein'
					}
				}
			},
			login_form_email: {
				validators: {
					notEmpty: {
						message: ''
					},
					emailAddress: {
						message: 'Bitte geben Sie eine g&uuml;ltige E-Mail Adresse an'
					}
				}
			}
		}
	});
});



$(document).ready(function() {
	$('.register_form').bootstrapValidator({
		message: 'This value is not valid',
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			register_form_username: {
				message: 'Der Benutzername ist ung&uuml;ltig',
				validators: {
					notEmpty: {
						message: 'Der Benutzername darf nicht leer sein'
					},
					stringLength: {
						min: 6,
						max: 30,
						message: 'Der Benutzername muss mindestens 6 und kleiner als 30 Zeichen lang sein'
					},
					regexp: {
						regexp: /^[a-zA-Z0-9_]+$/,
						message: 'Der Benutzername darf nur aus Buchstaben, Nummern und Unterstrichen bestehen'
					}
				}
			},
			register_form_email: {
				validators: {
					notEmpty: {
						message: 'Die Email Adresse ist leer'
					},
					emailAddress: {
						message: 'Bitte geben Sie eine gültige Email ein'
					}
				}
			},
			register_form_password: {
				validators: {
					notEmpty: {
						message: 'Das Passwort darf nicht leer sein'
					},
					identical: {
						field: 'register_form_password_confirm',
						message: 'Die Passwörter stimmen nicht überein'
					},
					different: {
						field: 'register_form_username',
						message: 'Das Passwort darf nicht gleich wie der Benutzername sein'
					},
					different: {
						field: 'register_form_email',
						message: 'Das Passwort darf nicht gleich wie die Email Adresse sein'
					},
					stringLength: {
						min: 6,
						message: 'Das Passwort muss mindestens 6 Zeichen lang sein'
					}
				}
			},
			register_form_password_confirm: {
				validators: {
					notEmpty: {
						message: 'Das Passwort darf nicht leer sein'
					},
					identical: {
						field: 'register_form_password',
						message: ' '
					},
					different: {
						field: 'register_form_username',
						message: 'Das Passwort darf nicht gleich wie der Benutzername sein'
					}
				}
			}
		}
	});
});
