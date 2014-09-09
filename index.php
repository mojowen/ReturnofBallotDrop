<?php

require_once('prepare.php');

$data = null;
$step = 1;

if( isset($_POST['KEY']) ) {

    $key = $_POST['KEY']; // '1jCTNodsee2a36JrZaQ-QY0t4VN24cwP3VFi3gG77ixA';

    $key = array_shift(explode('#', $key));
    $key = str_replace('/edit', '', $key);
    $key = end(explode('/', $key));

    $sheet_id = isset($_POST['SHEET_ID']) ? $_POST['SHEET_ID'] : null; // 'ovnuykr';

    $data = analyze_sheet($key, $sheet_id);

    $step = isset($_POST['SHEET_ID']) ? 3 : 2;
} elseif( isset($_POST['url']) ) {
    $step = 4;
}

?>
<html>
    <body>
    <?php if( $step === 1 ): ?>
        <h2>Analyze a Google Spreadsheet for a Voter Location Map</h2>
        <h3>STEP 1: Publish your Google Spreadsheet</h3>
        <p>See <a href="https://support.google.com/docs/answer/37579?hl=en" target="_blank">here</a> for instructions</p>
        <h3>STEP 2: Enter the Google Sheet's URL</h3>
        <form method="POST">
            <input type="text" name="KEY" placeholder="e.g. https://docs.google.com/spreadsheets/d/1jCTNodsee2a36JrZaQ-QY0t4VN24cwP3VFi3gG77ixA/edit" style="width:300px">
            <button type="submit">Next</button>
        </form>
    <?php elseif( $step === 2): ?>

        <h3>STEP 3: Select the WorkSheet you want to anaylze</h3>
        <form method="POST">
            <input type="hidden" name="KEY" value="<?php echo $key; ?>">
            <?php foreach ($data as $key => $value): ?>
                <input type="radio"
                       name="SHEET_ID"
                       value="<?php echo $value['id']; ?>"
                       label="<?php echo $value['title']; ?>">
                <label for="<?php echo $value['title']; ?>"><?php echo $value['title']; ?></label>
                <br/ >
            <?php endforeach; ?>
            <button type="submit">Next</button>
        </form>

    <?php elseif( $step === 3): ?>
        <h3>STEP 4: Customize Your Map</h3>

        <form method="POST">
            <h4>Type of Map Point</h4>
            <?php foreach ($data['types'] as $key => $value): ?>
                <label><?php echo $key; ?></label>
                <input type="text"
                       name="types[<?php echo $key; ?>][long_title]"
                       value="<?php echo $value['long_title']; ?>">
                <input type="text"
                       name="types[<?php echo $key; ?>][icon]"
                       value="<?php echo $value['icon']; ?>">
                <img src="<?php echo $value['icon']; ?>">
                Default Setting? <input type="checkbox" name="types[<?php echo $key; ?>][default]" value="true">
                <br />
            <?php endforeach; ?>

            <h4>Show Map Point as Innactive when labeled as</h4>
            <?php foreach ($data['states'] as $key => $value): ?>
                <input
                    type="checkbox"
                    name="states[<?php echo $key; ?>]"
                    <?php if( is_probably_innactive($value) ) echo "checked"; ?>>
                <label for="state[<?php echo $value; ?>"><?php echo $value; ?></label>
                <br />
            <?php endforeach; ?>

            <h4>Points will be filtered based on the following dates:</h4>
            <?php foreach ($data['dates'] as $key=>$value): ?>
                <h5><?php echo $value['label']; ?></h5>
                <input type="hidden"
                    value="<?php echo $value['date']; ?>"
                    name="dates[<?php echo $key; ?>][date]">
                <input type="hidden"
                    value="<?php echo $value['label']; ?>"
                    name="dates[<?php echo $key; ?>][label]">
            <?php endforeach; ?>

            <input type="hidden" name="url" value="<?php echo $data['url']; ?>">
            <button type="submit">Finish</button>
        </form>

    <?php elseif( $step === 4): ?>
        <h4>You're done! Copy and paste this into the body of the document you want to add the map</h4>
        <p>Note: Requires jQuery (for now)</p>
        <textarea style="width: 500px; height: 500px;" onclick="this.focus();this.select();"><script type="text/javascript" src="ElectionMap.js"></script>
<script type="text/javascript">
    ElectionMap.prototype.config = <?php echo json_encode($_POST); ?>
    document.body.onload = function() { var election_map = new ElectionMap(map) }
</script>
<div id="map" >
    <div id="canvas" style="width: 500px; height: 300px;"></div>
    <div id="list"><ul></ul></div>
</div></textarea>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script type="text/javascript" src="ElectionMap.js"></script>
        <script type="text/javascript">
            ElectionMap.prototype.config = <?php echo json_encode($_POST); ?>;
            ElectionMap.prototype.log_errors = true
            document.body.onload = function() { var election_map = new ElectionMap(map) }
        </script>
        <h2>Preview</h2>
        <div id="map" >
            <div id="canvas" style="width: 500px; height: 500px;"></div>
            <div id="list"><ul></ul></div>
        </div>
    <?php endif; ?>

    </body>
</html>
