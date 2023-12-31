module( 'plugins.time' );

test( '插入时间和日期', function () {
        var editor = te.obj[0];
        var range = te.obj[1];
        var body = editor.body;
        editor.setContent( '<p><br></p>' );
        range.setStart( body.firstChild, 0 ).collapse( 1 ).select();
        var date = new Date();
        var h = date.getHours();
        var min = date.getMinutes();
        min = (min < 10) ? ('0' + min) : min;
        var sec = date.getSeconds();
        sec = (sec < 10) ? ('0' + sec) : sec;
        editor.execCommand( 'time' );
        ua.manualDeleteFillData( editor.body );
        equal( ua.getChildHTML( body.firstChild ), h + ':' + min + ':' + sec + '<br>' );

        range.selectNode( body.firstChild.firstChild ).select();
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        month = (month < 10) ? ('0' + month) : month;
        var date = date.getDate();
        date = (date < 10) ? ('0' + date) : date;
        editor.execCommand( 'date' );
        ua.manualDeleteFillData( editor.body );
        equal( ua.getChildHTML( body.firstChild ), year + '-' + month + '-' + date + '<br>' );
} );

test( '表格插入时间和日期', function () {
        var editor = te.obj[0];
        var range = te.obj[1];
        var body = editor.body;
        var br = UE.browser.ie ? "" : "<br>";
        editor.setContent( '<table><tbody><tr><td></td><td></td></tr></tbody></table>' );
        var td = body.firstChild.getElementsByTagName( 'td' );
        range.setStart( td[0], 0 ).collapse( 1 ).select();
        var date = new Date();
        var h = date.getHours();
        var min = date.getMinutes();
        min = (min < 10) ? ('0' + min) : min;
        var sec = date.getSeconds();
        sec = (sec < 10) ? ('0' + sec) : sec;
        editor.execCommand( 'time' );
        ua.manualDeleteFillData(td[0]);
        equal( td[0].innerHTML, h + ':' + min + ':' + sec+br );
        /*选中一段内容插入日期*/
        range.setStart( td[1], 0 ).collapse( 1 ).select();
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        month = (month < 10) ? ('0' + month) : month;
        var date = date.getDate();
        date = (date < 10) ? ('0' + date) : date;
        editor.execCommand( 'date' );
        ua.manualDeleteFillData(td[1]);
        equal( td[1].innerHTML,  year + '-' + month + '-' + date +br);
} );
