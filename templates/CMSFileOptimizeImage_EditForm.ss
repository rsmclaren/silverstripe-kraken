<% if $IncludeFormTag %>
<form $FormAttributes data-layout-type="border">
    <% end_if %>
    <div class="cms-content-header north">
        <div class="cms-content-header-info">
            <% include CMSBreadcrumbs %>					
        </div>
        <% if $Fields.hasTabset %>
        <% with $Fields.fieldByName('Root') %>
        <div class="cms-content-header-tabs cms-tabset-nav-primary">
            <ul>
                <% loop $Tabs %>
                <li<% if $extraClass %> class="$extraClass"<% end_if %>><a href="#$id">$Title</a></li>
                <% end_loop %>
            </ul>
        </div>
        <% end_with %>
        <% end_if %>

        <!-- <div class="cms-content-search">...</div> -->
    </div>
    
    $EditFormTools
   
    <div class="cms-content-fields center <% if not $Fields.hasTabset %>cms-panel-padded<% end_if %>">
        <div id="no-images">
            <p><%t Kraken.NO_IMAGES "_There are currently no images to optimize." %></p>
            <a data-icon="back" class="backlink ss-ui-button cms-panel-link ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" href="$BackLink" role="button" aria-disabled="false"><span class="ui-button-text">Back to folder</span></a>
        </div>        
        
        <div id="optimize-info">
            <p class="wait-text"><%t Kraken.PLEASE_WAIT "_Please wait while the images are optimized. Depending on the number of images, this could take quite some time. Please do not leave this page until optimization is complete." %></p>
            
            <div id="optimize-progress-bar"></div>
            <div id="progress-count"><p><span id="count">0</span> &#47; <span id="max"></span></p></div>
        
            <ul id="file-list"></ul>
        </div>
    </div>

    <div class="cms-content-actions cms-content-controls south">
        <% if $Actions %>
        <div class="Actions">
            <% loop $Actions %>
            $Field
            <% end_loop %>
            <% if $Controller.LinkPreview %>
            <a href="$Controller.LinkPreview" class="cms-preview-toggle-link ss-ui-button" data-icon="preview">
                <%t LeftAndMain.PreviewButton 'Preview' %> &raquo;
            </a>
            <% end_if %>
        </div>
        <% end_if %>
    </div>
    <% if $IncludeFormTag %>
</form>
<% end_if %>
