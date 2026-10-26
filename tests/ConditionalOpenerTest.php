<?php

namespace IlBronza\FormField\Tests;

use IlBronza\FormField\Fields\BooleanFormField;
use IlBronza\FormField\Fields\TextFormField;
use IlBronza\FormField\FormFieldServiceProvider;
use Orchestra\Testbench\TestCase;

class ConditionalOpenerTest extends TestCase
{
	protected function getPackageProviders($app) : array
	{
		return [FormFieldServiceProvider::class];
	}

	public function test_boolean_opener_renders_its_radio_group_and_conditional_targets() : void
	{
		foreach ([true => 'true', false => 'false', 'null' => 'null'] as $value => $expected)
		{
			$field = new BooleanFormField([
				'name' => 'pdf_show', 'id' => 'pdf-show', 'type' => 'boolean',
				'value' => $value === 'null' ? null : (bool) $value,
				'showLabel' => false,
				'opener' => ['targetNames' => ['pdf_title', 'pdf_sorting_index'], 'value' => 'true'],
			]);
			$html = $field->render()->render();
			$this->assertMatchesRegularExpression('/value="' . $expected . '"\s+checked/', $html);
			$this->assertStringContainsString('const targetNames = ["pdf_title","pdf_sorting_index"]', $html);
			$this->assertStringContainsString('const expected = String("true")', $html);
			$this->assertStringNotContainsString('.fadeIn()', $html);
		}
	}

	public function test_legacy_opener_keeps_its_existing_behavior() : void
	{
		$field = new TextFormField([
			'name' => 'source', 'type' => 'text', 'value' => '', 'showLabel' => false,
			'opener' => ['targetName' => 'destination', 'required' => true],
		]);
		$html = $field->render()->render();
		$this->assertStringContainsString('.fadeIn()', $html);
		$this->assertStringContainsString('.prop(\'required\', true)', $html);
		$this->assertStringNotContainsString('const targetNames', $html);
	}
}
