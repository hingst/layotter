<div class="layotter-modal-prompt">
    <div class="layotter-modal-prompt-message">
        <p>
            {{ prompt.message }}
        </p>
        <p>
            <input id="layotter-modal-prompt-input" type="text" value="{{ prompt.initialValue }}">
        </p>
    </div>
    <div class="layotter-modal-prompt-buttons">
        <button type="button" class="button button-primary button-large" ng-click="prompt.okAction()">{{ prompt.okText }}</button>
        <button type="button" class="button button-large" ng-click="prompt.cancelAction()">{{ prompt.cancelText }}</button>
    </div>
</div>