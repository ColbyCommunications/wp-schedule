<?php

use PHPUnit\Framework\TestCase;
use ColbyComms\Schedules\SchedulePickerShortcode;

class SchedulePickerShortcodeTest extends TestCase {
	public function test_shortcode_output() {
		$form = '
<form>
	<select name="event-tag" onChange="this.form.submit()">
		<option>-- Select class year --</option>
		<option value="class-of-2017">Class of 2017</option>
		<option value="class-of-2013">Class of 2013</option>
		<option value="class-of-2008">Class of 2008</option>
		<option value="class-of-2003">Class of 2003</option>
	</select>
</form>';

		ob_start();

		echo SchedulePickerShortcode::render(
			[
				'text' => 'Select class year',
				'terms' => [
					'class-of-2017' => 'Class of 2017',
					'class-of-2013' => 'Class of 2013',
					'class-of-2008' => 'Class of 2008',
					'class-of-2003' => 'Class of 2003',
				],
			]
		);

		$from_function = ob_get_clean();

		// The regex removes all whitespace.
		$this->assertEquals( preg_replace('/\s+/', '', $form ), preg_replace('/\s+/', '', $from_function ) );
	}

	public function test_build_term_array() {
		$terms = [
			(object) [
				'name' => 'Class of 2017',
				'slug' => 'class-of-2017',
			],
			(object) [
				'name' => 'Class of 2013',
				'slug' => 'class-of-2013',
			],
			(object) [
				'name' => 'Class of 2008',
				'slug' => 'class-of-2008',
			],
			(object) [
				'name' => 'Class of 2003',
				'slug' => 'class-of-2003',
			]
		];
		
		$parsed_terms = SchedulePickerShortcode::make_term_array( $terms );

		$this->assertEquals(
			$parsed_terms,
			[
				'class-of-2017' => 'Class of 2017',
				'class-of-2013' => 'Class of 2013',
				'class-of-2008' => 'Class of 2008',
				'class-of-2003' => 'Class of 2003',
			]
		);
	}
}