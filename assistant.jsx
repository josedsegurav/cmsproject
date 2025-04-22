

class AIAssistant extends React.Component {
  constructor(props) {
    super(props);
    this.state = { 
      prompt: '',
      response: 'response: ',
      isLoading: false,
      error: null,
      dbData: null,
      isLoadingData: false
    };
    
    // Your API key
    this.apiKey = "AIzaSyC8NdFntuXa83f0YTEzz6SUmgbgDrJWGu4";
  }
  
  componentDidMount() {
    // Load database data when component mounts
    this.fetchDatabaseData();
  }
  
  fetchDatabaseData = async () => {
    this.setState({ isLoadingData: true });
    
    try {
      // Fetch data from your PHP endpoint
      const response = await fetch('get_items.php');
      
      if (!response.ok) {
        throw new Error(`Failed to fetch database data: ${response.status}`);
      }
      
      const data = await response.json();
      this.setState({ 
        dbData: data,
        isLoadingData: false 
      });
      console.log("Database data loaded:", data);
    } catch (error) {
      console.error("Error fetching database data:", error);
      this.setState({ 
        error: "Error loading database data: " + error.message,
        isLoadingData: false
      });
    }
  }

  handlePromptChange = (event) => {
    this.setState({ prompt: event.target.value });
  }
  
  handleClick = async () => {
    if (!this.state.prompt.trim()) {
      this.setState({ response: "Please enter a prompt" });
      return;
    }
    this.setState({ isLoading: true });
    
    try {
      // Prepare prompt with database data context
      const contextualPrompt = this.preparePromptWithContext(this.state.prompt);
      
      // Make a direct fetch call to the Google Generative AI API
      const response = await fetch(
        `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${this.apiKey}`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            contents: [
              {
                parts: [
                  {
                    text: contextualPrompt
                  }
                ]
              }
            ]
          })
        }
      );
      
      if (!response.ok) {
        throw new Error(`API request failed with status: ${response.status}`);
      }
      
      const data = await response.json();
      
      // Extract the text from the response
      if (data.candidates && data.candidates[0] && data.candidates[0].content) {
        const text = data.candidates[0].content.parts[0].text;
        this.setState({ 
          response: text,
          isLoading: false
        });
      } else {
        throw new Error("Unexpected response format");
      }
    } catch (error) {
      console.error("Error generating content:", error);
      this.setState({ 
        response: "Error generating content: " + error.message,
        isLoading: false
      });
    }
  }
  
  preparePromptWithContext(userPrompt) {
    // If we don't have database data yet, just use the original prompt
    if (!this.state.dbData) {
      return userPrompt;
    }
    
    // Format the database data as context
    const contextData = JSON.stringify(this.state.dbData, null, 2);
    
    // Construct a prompt that includes both the context and the user's question
    return `Based on the following database items:
${contextData}
User question: ${userPrompt}`;
  }

  render() {
    return (
      <>
        <div>
          <h2>Assistant</h2>
          <p>Ask me anything about the database items!</p>
          
          {/* Show data loading status */}
          {this.state.isLoadingData ? 
            <p>Loading database data...</p> : 
            <p>{`${this.state.dbData ? this.state.dbData.length : 0} items loaded from database`}</p>
          }
          
          <input 
            type="text" 
            id="prompt" 
            value={this.state.prompt}
            onChange={this.handlePromptChange}
          />
          <button 
            id="send"
            onClick={this.handleClick}
            disabled={this.state.isLoading || this.state.isLoadingData}
          >
            {this.state.isLoading ? 'Loading...' : 'Ask'}
          </button>
        </div>
        
        {/* Error message if present */}
        {this.state.error && <p style={{ color: 'red' }}>{this.state.error}</p>}
        
        {/* Response paragraph */}
        <p id="response">{this.state.response}</p>
      </>
    );
  }
}

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
  const domContainer = document.getElementById('assistant-container');
  if (domContainer) {
    const root = ReactDOM.createRoot(domContainer);
    root.render(<AIAssistant />);
  } else {
    console.error('Container element not found');
  }
});