// USE WORDWRAP AND MAXIMIZE THE WINDOW TO SEE THIS FILE
c_styles={};c_menus={}; // do not remove this line

// You can remove most comments from this file to reduce the size if you like.




/******************************************************
	(1) GLOBAL SETTINGS
*******************************************************/

c_hideTimeout=500; // 1000==1 second
c_subShowTimeout=250;
c_keepHighlighted=true;
c_findCURRENT=false; // find the item linking to the current page and apply it the CURRENT style class
c_findCURRENTTree=true;
c_overlapControlsInIE=true;
c_rightToLeft=false; // if the menu text should have "rtl" direction (e.g. Hebrew, Arabic)




/******************************************************
	(2) MENU STYLES (CSS CLASSES)
*******************************************************/

// You can define different style classes here and then assign them globally to the menu tree(s)
// in section 3 below or set them to any UL element from your menu tree(s) in the page source


c_imagesPath=""; // path to the directory containing the menu images


c_styles['MM']=[ // MainMenu (the shorter the class name the better)
[
// MENU BOX STYLE
0,		// BorderWidth
'solid',	// BorderStyle (CSS valid values except 'none')
'#666666',	// BorderColor ('color')
0,		// Padding
'[dropdowntabfiles/media/bluetab.gif]',	// Background ('color','transparent','[image_source]')
'',		// IEfilter (only transition filters work well - not static filters)
''		// Custom additional CSS for the menu box (valid CSS)
],[
// MENU ITEMS STYLE
1,		// BorderWidth
'solid',	// BorderStyle (CSS valid values except 'none')
'solid',	// OVER BorderStyle
'#F0F0F0',	// BorderColor ('color')
'#666666',	// OVER BorderColor
4,		// Padding
'[dropdowntabfiles/media/bluetab.gif]',	// Background ('color','transparent','[image_source]')
'[dropdowntabfiles/media/bluetabactive.gif]',	// OVER Background
'#252455',	// Color
'#000000',	// OVER Color
'12px',		// FontSize (values in CSS valid units - %,em,ex,px,pt)
'verdana,arial,helvetica,sans-serif',	// FontFamily
'bold',		// FontWeight (CSS valid values - 'bold','normal','bolder','lighter','100',...,'900')
'none',		// TextDecoration (CSS valid values - 'none','underline','overline','line-through')
'none',		// OVER TextDecoration
'left',		// TextAlign ('left','center','right','justify')
1,		// ItemsSeparatorSize
'solid',	// ItemsSeparatorStyle (border-style valid values)
'transparent',	// ItemsSeparatorColor ('color','transparent')
0,		// ItemsSeparatorSpacing
true,			// UseSubMenuImage (true,false)
'[h_arrow.gif]',	// SubMenuImageSource ('[image_source]')
'[h_arrow_over.gif]',	// OverSubMenuImageSource
7,			// SubMenuImageWidth
4,			// SubMenuImageHeight
'10',			// SubMenuImageVAlign ('pixels from item top','middle')
'solid',		// VISITED BorderStyle
'#FFFFFF',		// VISITED BorderColor
'[dropdowntabfiles/media/bluetab.gif]',		// VISITED Background
'#252455',		// VISITED Color
'none',			// VISITED TextDecoration
'[h_arrow.gif]',	// VISITED SubMenuImageSource
'solid',		// CURRENT BorderStyle
'#666666',		// CURRENT BorderColor
'#F0F0F0',		// CURRENT Background
'#252455',		// CURRENT Color
'none',			// CURRENT TextDecoration
'[h_arrow.gif]',	// CURRENT SubMenuImageSource
'',		// Custom additional CSS for the items (valid CSS)
'',		// OVER Custom additional CSS for the items (valid CSS)
'',		// CURRENT Custom additional CSS for the items (valid CSS)
''		// VISITED Custom additional CSS for the items (valid CSS)
]];


c_styles['SM']=[ // SubMenus
[
// MENU BOX STYLE
1,		// BorderWidth
'solid',	// BorderStyle (CSS valid values except 'none')
'#666666',	// BorderColor ('color')
3,		// Padding
'#FFFFFF',	// Background ('color','transparent','[image_source]')
'',		// IEfilter (only transition filters work well - not static filters)
''		// Custom additional CSS for the menu box (valid CSS)
],[
// MENU ITEMS STYLE
1,		// BorderWidth
'solid',	// BorderStyle (CSS valid values except 'none')
'solid',	// OVER BorderStyle
'#F0F0F0',	// BorderColor ('color')
'#666666',	// OVER BorderColor
3,		// Padding
'#FFFFFF',	// Background ('color','transparent','[image_source]')
'#F0F0F0',	// OVER Background
'#252455',	// Color
'#000000',	// OVER Color
'12px',		// FontSize (values in CSS valid units - %,em,ex,px,pt)
'verdana,arial,helvetica,sans-serif',	// FontFamily
'normal',	// FontWeight (CSS valid values - 'bold','normal','bolder','lighter','100',...,'900')
'none',		// TextDecoration (CSS valid values - 'none','underline','overline','line-through')
'none',		// OVER TextDecoration
'left',		// TextAlign ('left','center','right','justify')
0,		// ItemsSeparatorSize
'solid',	// ItemsSeparatorStyle (border-style valid values)
'transparent',	// ItemsSeparatorColor ('color','transparent')
2,		// ItemsSeparatorSpacing
true,			// UseSubMenuImage (true,false)
'[v_arrow.gif]',	// SubMenuImageSource ('[image_source]')
'[v_arrow_over.gif]',	// OverSubMenuImageSource
7,			// SubMenuImageWidth
7,			// SubMenuImageHeight
'7',			// SubMenuImageVAlign ('pixels from item top','middle')
'solid',		// VISITED BorderStyle
'#FFFFFF',		// VISITED BorderColor
'#FFFFFF',		// VISITED Background
'#252455',		// VISITED Color
'none',			// VISITED TextDecoration
'[v_arrow.gif]',	// VISITED SubMenuImageSource
'solid',		// CURRENT BorderStyle
'#666666',		// CURRENT BorderColor
'#F0F0F0',		// CURRENT Background
'#252455',		// CURRENT Color
'none',			// CURRENT TextDecoration
'[v_arrow.gif]',	// CURRENT SubMenuImageSource
'',		// Custom additional CSS for the items (valid CSS)
'',		// OVER Custom additional CSS for the items (valid CSS)
'',		// CURRENT Custom additional CSS for the items (valid CSS)
''		// VISITED Custom additional CSS for the items (valid CSS)
]];




/******************************************************
	(3) MENU TREE FEATURES
*******************************************************/

// Normally you would probably have just one menu tree (i.e. one main menu with sub menus).
// But you are actually not limited to just one and you can have as many menu trees as you like.
// Just copy/paste a config block below and configure it for another UL element if you like.


c_menus['Menu1']=[ // the UL element with id="Menu1"
[
// MAIN-MENU FEATURES
'horizontal',	// ItemsArrangement ('vertical','horizontal')
'relative',	// Position ('relative','absolute','fixed')
'2em',		// X Position (values in CSS valid units- px,em,ex)
'2em',		// Y Position (values in CSS valid units- px,em,ex)
false,		// RightToLeft display of the sub menus
false,		// BottomToTop display of the sub menus
0,		// X SubMenuOffset (pixels)
0,		// Y SubMenuOffset
'10em',		// Width (values in CSS valid units - px,em,ex) (matters for main menu with 'vertical' ItemsArrangement only)
'MM',		// CSS Class (one of the defined in section 2)
false		// Open sub-menus onclick (default is onmouseover)
],[
// SUB-MENUS FEATURES
5,		// X SubMenuOffset (pixels)
1,		// Y SubMenuOffset
'auto',		// Width ('auto',values in CSS valid units - px,em,ex)
'100',		// MinWidth ('pixels') (matters/useful if Width is set 'auto')
'500',		// MaxWidth ('pixels') (matters/useful if Width is set 'auto')
'SM',		// CSS Class (one of the defined in section 2)
false		// Open sub-menus onclick (default is onmouseover)
]];