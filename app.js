$(function() {
	// Get task list via Ajax
	$.get("api.php", {get: 'all'}, function(tasks) {
		$.each(tasks, function(index, task) {
			if(task.done == 1) {
				build_task(task.subject, task.id).appendTo("#done");
				$("#done input[type=checkbox]").attr("checked", "checked");
			} else {
				build_task(task.subject, task.id).appendTo("#tasks");
			}
		});

		$("#spinner").remove();
		update_count();

	}, 'json');

	// Add new task on button click
	$("#new-task button").click(function() {
		task = $("#new-task input").val();

		if(!task) return false;

		$("#new-task input, #new-task button").attr("disabled", "disabled");

		$.post("api.php", {type: 'add', subject: task}, function (newid) {

			$("#new-task input, #new-task button").removeAttr("disabled");

			if(newid) {
				build_task(task, newid).appendTo("#tasks").hide().fadeIn();
				update_count();

				$("#new-task input").val("").focus();
			} else {
				alert("Task not added");
			}
		});
	});

	// Add new task when press Enter key in Textbox
	$("#new-task input").keydown(function(e) {
		if(e.which == 13)
			$("#new-task button").click();
	});
});

// Task <li> element constructor
function build_task(msg, id) {
	checkbox = $("<input>", {
		type: "checkbox"
	}).click(function() {
		if($(this).is(":checked")) {
			li = $(this).parent();

			$.post("api.php", {type: 'done', id: li.data('id')}, function(data) {
				if(data) {
					li.prependTo("#done").hide().fadeIn();
					update_count();
				} else {
					alert("Cannot check task as completed");
				}
			});
		} else {
			li = $(this).parent();

			$.post("api.php", {type: 'undo', id: li.data('id')}, function(data) {
				if(data) {
					li.appendTo("#tasks").hide().fadeIn();
					update_count();
				} else {
					alert("Cannot check task as incompleted");
				}
			});
		}
	});

	task = $("<span>").text(msg);

	del = $("<a>", {
		href: "#"
	}).html("&times;").click(function() {
		$(this).parent().fadeOut(function () {
			li = $(this);

			$.post("api.php", {type: 'delete', id: li.data('id')}, function(data) {
				if(data) {
					li.remove();
					update_count();
				} else {
					alert("Unable to delete task");
				}
			});
		});
	});

	return $("<li>").data("id", id).append(checkbox).append(task).append(del);
}

function update_count() {
	$("h1 span").html( $("#tasks li").length );
}

