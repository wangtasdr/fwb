module( "plugins.selectall" );
test( 'normal', function () {
        var editor = te.obj[0], db = editor.body;
        editor.setContent( '<p><em>xxxx</em></p>ssss' );
        editor.focus();
        editor.execCommand( 'selectAll' );
        //equal( UE.plugins['selectall'].notNeedUndo, 1, "notNeedUndo==1" );
        editor.execCommand( "bold" );
        equal( ua.getChildHTML( db ), "<p><strong><em>xxxx</em></strong></p><p><strong>ssss</strong></p>", "after calling selectAll command" );
} );

test( 'a part of the content is selected', function () {
        var editor = te.obj[0], d = editor.document, range = te.obj[1], db = editor.body;
        editor.setContent( '<p><em>xxxx</em></p>ssss' );
        range.selectNode( db.lastChild.firstChild ).select();
        editor.execCommand( "bold" );
        equal( ua.getChildHTML( db ), "<p><em>xxxx</em></p><p><strong>ssss</strong></p>", "before calling selectAll command" );
        editor.execCommand( 'selectAll' );
        //equal( UE.plugins['selectall'].notNeedUndo, 1, "notNeedUndo==1" );
        editor.execCommand( "bold" );
        equal( ua.getChildHTML( db ), "<p><strong><em>xxxx</em></strong></p><p><strong>ssss</strong></p>", "after calling selectAll command" );
} );

test( 'trace1743 :content is null', function () {
        var editor = te.obj[0];
        var range = te.obj[1];
        editor.setContent( '<p><br></p>' );
        //TODO 现在必须先focus再selectall，trace1743
        editor.execCommand( 'selectAll' );
        equal( ua.getChildHTML( editor.body ), "<p><br></p>", "content is null" );
        //equal(UE.plugins['selectall'].notNeedUndo, 1, "notNeedUndo==1" );
        range.setStart( editor.body.firstChild, 0 ).collapse( 1 ).select();
        editor.execCommand( "bold" );
        ua.manualDeleteFillData( editor.body );
        equal( ua.getChildHTML( editor.body ), "<p><strong></strong><br></p>", "after calling command bold" );
} );