<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$this->setFrameMode(true);?>
<?if($arResult):?>
	<div class="menu top">
		<ul class="top">
			<?foreach($arResult as $arItem):?>
				<?$bShowChilds = $arParams['MAX_LEVEL'] > 1;?>
				<?$bParent = $arItem['CHILD'] && $bShowChilds;?>
				<li class="<?=($arItem['SELECTED'] ? 'selected' : '')?>">
					<a class="dark-color<?=($bParent ? ' parent' : '')?> <?= $arItem['PARAMS']['CLASS']?>" href="<?=$arItem["LINK"]?>" title="<?=$arItem["TEXT"]?>">
						<span><?=$arItem['TEXT']?></span>
						<?if($bParent):?>
							<span class="arrow"><i class="svg svg_triangle_right"></i></span>
						<?endif;?>
					</a>
					<?if($bParent):?>
						<ul class="dropdown">
							<li class="menu_back"><a href="#" class="dark-color" rel="nofollow"><i class="svg svg-arrow-right"></i><?=GetMessage('NEXT_T_MENU_BACK')?></a></li>
							<li class="menu_title"><a href="<?=$arItem['LINK'];?>"><?=$arItem['TEXT']?></a></li>
							<?foreach($arItem['CHILD'] as $arSubItem):?>
								<?$bShowChilds = $arParams['MAX_LEVEL'] > 2;?>
								<?$bParent = $arSubItem['CHILD'] && $bShowChilds;?>
								<? /* if(strpos($arSubItem['LINK'], 'novyy_god') != false) {
									$arSubItem['HAS_BACKGROUND'] = true;
								} */ ?>
								<li class="<?=($arSubItem['SELECTED'] ? ' selected' : '')?> ">
									<a class="dark-color<?=($bParent ? ' parent' : '')?> <?=($arSubItem["HAS_BACKGROUND"] ? "has_background" : "")?>" href="<?=$arSubItem["LINK"]?>" title="<?=$arSubItem["TEXT"]?>">
										<span><?=$arSubItem['TEXT']?></span>
										<?if($bParent):?>
											<span class="arrow"><i class="svg svg_triangle_right"></i></span>
										<?endif;?>
									</a>
									<?if($bParent):?>
										<ul class="dropdown">
											<li class="menu_back"><a href="#" class="dark-color" rel="nofollow"><i class="svg svg-arrow-right"></i><?=GetMessage('NEXT_T_MENU_BACK')?></a></li>
											<li class="menu_title"><a href="<?=$arSubItem['LINK'];?>"><?=$arSubItem['TEXT']?></a></li>
											<?foreach($arSubItem["CHILD"] as $arSubSubItem):?>
												<?$bShowChilds = $arParams['MAX_LEVEL'] > 3;?>
												<?$bParent = $arSubSubItem['CHILD'] && $bShowChilds;?>
												<?if(strpos($arSubSubItem['LINK'], 'novyy_god') != false) {
													$arSubSubItem['HAS_BACKGROUND'] = true;
												} ?>
												<li <?=($arSubSubItem['SELECTED'] ? ' class="selected"' : '')?>>
													<a class="dark-color <?=($bParent ? 'parent' : '')?> <?=($arSubSubItem["HAS_BACKGROUND"] ? "has_background" : "")?>" href="<?=$arSubSubItem["LINK"]?>" title="<?=$arSubSubItem["TEXT"]?>">
														<span><?=$arSubSubItem['TEXT']?></span>
														<?if($bParent):?>
															<span class="arrow"><i class="svg svg_triangle_right"></i></span>
														<?endif;?>
													</a>
													<?if($bParent):?>
														<ul class="dropdown">
															<li class="menu_back"><a href="#" class="dark-color" rel="nofollow"><i class="svg svg-arrow-right"></i><?=GetMessage('NEXT_T_MENU_BACK')?></a></li>
															<li class="menu_title"><a href="<?=$arSubSubItem['LINK'];?>"><?=$arSubSubItem['TEXT']?></a></li>
															<?foreach($arSubSubItem["CHILD"] as $arSubSubSubItem):?>
																<li <?=($arSubSubSubItem['SELECTED'] ? ' class="selected"' : '')?>>
																	<?if(strpos($arSubSubSubItem['LINK'], 'novyy_god') != false) {
																		$arSubSubSubItem['HAS_BACKGROUND'] = true;
																	} ?>
																	<a class="dark-color <?=($arSubSubSubItem["HAS_BACKGROUND"] ? "has_background" : "")?>" href="<?=$arSubSubSubItem["LINK"]?>" title="<?=$arSubSubSubItem["TEXT"]?>">
																		<span><?=$arSubSubSubItem['TEXT']?></span>
																	</a>
																</li>
															<?endforeach;?>
														</ul>
													<?endif;?>
												</li>
											<?endforeach;?>
										</ul>
									<?endif;?>
								</li>
							<?endforeach;?>
						</ul>
					<?endif;?>
				</li>
			<?endforeach;?>
		</ul>
	</div>
<?endif;?>