jQuery(document).ready((function(e){e("select#role").change((function(){guestAuthorRole.role!==e(this).val()?(e('label[for="email"] > span.description').show(),e("#createuser input[name=email]").closest("tr").addClass("form-required"),"new"===guestAuthorRole.screen?e('label[for="user_login"]')[0].innerHTML=r:e('label[for="user_login"]').parents("tr").show(),e('label[for="pass1"]').parents("tr").show(),e("input#send_user_notification").parents("tr").show(),e('label[for="rich_editing"]').parents("tr").show(),e('label[for="comment_shortcuts"]').parents("tr").show(),e('label[for="admin_bar_front"]').parents("tr").show(),e('label[for="locale"]').parents("tr").show(),e("tr.user-admin-color-wrap").show()):(e('label[for="email"] > span.description').hide(),e("#createuser input[name=email]").closest("tr").removeClass("form-required"),"new"===guestAuthorRole.screen?e('label[for="user_login"]')[0].innerHTML=guestAuthorRole.displayNameLabel:e('label[for="user_login"]').parents("tr").hide(),e('label[for="pass1"]').parents("tr").hide(),e("input#send_user_notification").parents("tr").hide(),e('label[for="rich_editing"]').parents("tr").hide(),e('label[for="comment_shortcuts"]').parents("tr").hide(),e('label[for="admin_bar_front"]').parents("tr").hide(),e('label[for="locale"]').parents("tr").hide(),e("tr.user-admin-color-wrap").hide())}));const r=e('label[for="user_login"]')[0].innerHTML;e("select#role").change()}));