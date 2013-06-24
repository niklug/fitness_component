var i18n = jQuery.extend({}, i18n || {}, {
    dcmvcal: {
        dateformat: {
            "fulldaykey": "ddMMyyyy",
            "fulldayshow": "d L yyyy",
            "fulldayvalue": "d/M/yyyy", 
            "Md": "W d/M",
            "nDaysView": "d/M",
            "Md3": "d L",
            "separator": "/",
            "year_index": 2,
            "month_index": 1,
            "day_index": 0,
            "day": "d",
            "sun2": "Do",
            "mon2": "Se",
            "tue2": "Te",
            "wed2": "Qu",
            "thu2": "Qu",
            "fri2": "Se",
            "sat2": "Sa",
            "sun": "Dom",
            "mon": "Seg",
            "tue": "Ter",
            "wed": "Qua",
            "thu": "Qui",
            "fri": "Sex",
            "sat": "Sab",
            "sunday": "Sunday",
            "monday": "Monday",
            "tuesday": "Tuesday",
            "wednesday": "Wednesday",
            "thursday": "Thursday",
            "friday": "Friday",
            "saturday": "Saturday",
            "jan": "Jan",
            "feb": "Fev",
            "mar": "Mar",
            "apr": "Abr",
            "may": "Mai",
            "jun": "Jun",
            "jul": "Jul",
            "aug": "Ago",
            "sep": "Set",
            "oct": "Out",
            "nov": "Nov",
            "dec": "Dez",
            "l_jan": "Janeiro",
            "l_feb": "Fevereiro",
            "l_mar": "Março",
            "l_apr": "Abril",
            "l_may": "Maio",
            "l_jun": "Junho",
            "l_jul": "Julho",
            "l_aug": "Agosto",
            "l_sep": "Setembro",
            "l_oct": "Outubro",
            "l_nov": "Novembro",
            "l_dec": "Dezembro"
        },
        "no_implemented": "Ainda não implementado",
        "to_date_view": "Clique aqui para ver a data de hoje",
        "i_undefined": "Indefinido",
        "allday_event": "Evento do dia enteiro",
        "repeat_event": "Repetir evento",
        "time": "Hora",
        "event": "Evento",
        "location": "Lugar",
        "participant": "Participante",
        "get_data_exception": "Erro ao obter datos",
        "new_event": "Novo evento",
        "confirm_delete_event": "Confirma a exclusão deste evento?",
        "confrim_delete_event_or_all": "Deseja eliminar todos os eventos repetidos o somente este evento? \r\n Clique [OK] para eliminar somente este evento, clique [Cancelar] para eleminar todos os eventos repetidos.",
        "data_format_error": "Erro com o formato dos datos",
        "invalid_title": "Titulo do evento não pode estar em branco o conter ($<>) ",
        "view_no_ready": "A imagem não está pronta",
        "example": "Por exemplo, reunião na sala 107",
        "content": "O que",
        "create_event": "Criar evento",
        "update_detail": "Modificar detalhes",
        "click_to_detail": "Veja mais detalhes",
        "i_delete": "Excluir",
        "i_save": "Salvar",
        "i_close": "Fechar",
        "day_plural": "Dias",
        "others": "Outros",
        "item": "",
        "loading_data":"Carregando dados...",
        "request_processed":"A sua solicitação está sendo processada...",
        "success":"Sucesso!",
        "are_you_sure_delete":"Tem certeza que deseja deletar este evento?",
        "ok":"OK",
        "cancel":"Cancelar",
        "manage_the_calendar":"Gerir o calendario",
        "error_occurs":"Erro",
        "color":"Cor",
        "invalid_date_format":"Formato da data Inválido",
        "invalid_time_format":"Formato da hora Inválido",
        "_simbol_not_allowed":"$<> Não permitido",
        "subject":"Assunto",
        "time":"Hora",
        "to":"A",
        "all_day_event":"Evento de dia inteiro",
        "location":"Lugar",
        "remark":"Descrição",
        "click_to_create_new_event":"Clique para criar um novo evento",
        "new_event":"Novo evento",
        "click_to_back_to_today":"Clique para voltar ao dia de hoje",
        "today":"Hoje",
        "sday":"Dia",
        "week":"Semana",
        "month":"mês",
        "ndays":"Dia",
        "nmonth":"nMês",
        "refresh_view":"Atualizar visao",
        "refresh":"refrescar",
        "prev":"Prec.",
        "next":"Próx.",
        "loading":"Seus dados estao sendo transferidos",
        "error_overlapping":"This event is overlapping another event",
        "sorry_could_not_load_your_data":"Desculpe, nao foi possivel carregar os seus dados. Por favor, tente novamente mais tarde.",
        "first":"First",
        "second":"Second",
        "third":"Third",
        "fourth":"Fourth",
        "last":"last",
        "repeat":"Repeat: ",
        "edit":"Edit",
        "edit_recurring_event":"Edit recurring event",
        "would_you_like_to_change_only_this_event_all_events_in_the_series_or_this_and_all_following_events_in_the_series":"Would you like to change only this event, all events in the series, or this and all following events in the series?",
        "only_this_event":"Only this event",
        "all_other_events_in_the_series_will_remain_the_same":"All other events in the series will remain the same.",
        "following_events":"Following events",
        "this_and_all_the_following_events_will_be_changed":"This and all the following events will be changed.",
        "any_changes_to_future_events_will_be_lost":"Any changes to future events will be lost.",
        "all_events":"All events",
        "all_events_in_the_series_will_be_changed":"All events in the series will be changed.",
        "any_changes_made_to_other_events_will_be_kept":"Any changes made to other events will be kept.",
        "cancel_this_change":"Cancel this change",
        "delete_recurring_event":"Delete recurring event",
        "would_you_like_to_delete_only_this_event_all_events_in_the_series_or_this_and_all_future_events_in_the_series":"Would you like to delete only this event, all events in the series, or this and all future events in the series?",
        "only_this_instance":"Only this instance",
        "all_other_events_in_the_series_will_remain":"All other events in the series will remain.",
        "all_following":"All following",
        "this_and_all_the_following_events_will_be_deleted":"This and all the following events will be deleted.",
        "all_events_in_the_series":"All events in the series",
        "all_events_in_the_series_will_be_deleted":"All events in the series will be deleted.",
        "repeats":"Repeats",
        "daily":"Daily",
        "every_weekday_monday_to_friday":"Every weekday (Monday to Friday)",
        "every_monday_wednesday_and_friday":"Every Monday, Wednesday, and Friday",
        "every_tuesday_and_thursday":"Every Tuesday, and Thursday",
        "weekly":"Weekly",
        "monthly":"Monthly",
        "yearly":"Yearly",
        "repeat_every":"Repeat every:",
        "weeks":"weeks",
        "repeat_on":"Repeat on:",
        "repeat_by":"Repeat by:",
        "day_of_the_month":"day of the month",
        "day_of_the_week":"day of the week",
        "starts_on":"Starts on:",
        "ends":"Ends:",
        "never":" Never",
        "after":"After",
        "occurrences":"occurrences",
        "summary":"Summary:",
        "every":"Every",
        "weekly_on_weekdays":"Weekly on weekdays",
        "weekly_on_monday_wednesday_friday":"Weekly on Monday, Wednesday, Friday",
        "weekly_on_tuesday_thursday":"Weekly on Tuesday, Thursday",
        "on":"on",
        "on_day":"on day",
        "on_the":"on the",
        "months":"months",
        "annually":"Annually",
        "years":"years",
        "once":"Once",
        "times":"times",
        "until":"until"
    }
});