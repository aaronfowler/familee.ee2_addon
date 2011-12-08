#Familee for ExpressionEngine 2 - Playa 4 version

Familee outputs an unordered list of forward/reverse relationship links with no duplicates.

Version: 2.0.3

Author: Aaron Fowler (http://twitter.com/adfowler)

License: Apache License v2.0


##Installation

Place the "familee" folder inside the ExpressionEngine third-party directory


##Usage

	{exp:familee entry_id="1234"}


##Parameters

1) entry_id - This is the only required parameter. Allows you to specify entry id number.

2) channel_id - Allows you to limit relationships to within one or more channels. Separate multiple channels with a pipe character.

3) orderby='entry_date' - Options are 'entry_date', 'title', or 'entry_id'

4) sort='DESC' - Options are 'ASC' or 'DESC'

5) path - Prepend a path to the returned url.

6) class - Add a class attribute to the `<ul>` tag.

7) id - Add an id attribute to the `<ul>` tag.

6) html_start - Add HTML before the opening `<ul>` tag.

7) html_end - Add HTML after the closing `</ul>` tag.

8) include_entry_id="yes" - output the entry_id of the related links in the url. E.g., http://example.com/1234/my_entry_title_


##Example

	{exp:familee entry_id="{entry_id}" channel_id="1|2|3" orderby="title" sort="asc" path="/{segment_2}/" class="nav" id="article-nav" html_start="<h4>Related Links</h4>" html_end="<p>That's all, folks!</p>"}


##Output

	<h4>Related Links</h4>
	<ul class="nav" id="article-nav">
		<li><a href="/[segment_2]/[url_title]">[title]</a></li>
		<li>...
	</ul>
	<p>That's all, folks!</p>
